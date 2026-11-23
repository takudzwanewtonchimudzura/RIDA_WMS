import fs from 'node:fs';
import path from 'node:path';

const input = process.argv[2];
if (!input) throw new Error('Supply the directory containing OCHA zwe_admin2.geojson and zwe_admin3.geojson.');
const districts = JSON.parse(fs.readFileSync(path.join(input, 'zwe_admin2.geojson'), 'utf8'));
const wards = JSON.parse(fs.readFileSync(path.join(input, 'zwe_admin3.geojson'), 'utf8'));
const result = {};
// Five-metre-scale simplification keeps local validation practical without a GIS server.
const simplify = (points) => {
    if (points.length <= 4) return points;
    const first = points[0], last = points.at(-1);
    const dx = last[0] - first[0], dy = last[1] - first[1];
    let maxDistance = 0, split = 0;
    for (let i = 1; i < points.length - 1; i++) {
        const t = dx || dy ? Math.max(0, Math.min(1, ((points[i][0] - first[0]) * dx + (points[i][1] - first[1]) * dy) / (dx * dx + dy * dy))) : 0;
        const distance = (points[i][0] - first[0] - t * dx) ** 2 + (points[i][1] - first[1] - t * dy) ** 2;
        if (distance > maxDistance) { maxDistance = distance; split = i; }
    }
    return maxDistance > 0.00005 ** 2
        ? [...simplify(points.slice(0, split + 1)).slice(0, -1), ...simplify(points.slice(split))]
        : [first, last];
};
const ring = (points) => {
    const simplified = simplify(points);
    return (simplified.length >= 4 ? simplified : points).map(point => point.map(value => Number(value.toFixed(7))));
};
for (const feature of districts.features) {
    const p = feature.properties;
    result[p.adm1_name] ??= {};
    const geometry = feature.geometry;
    geometry.coordinates = geometry.type === 'Polygon' ? geometry.coordinates.map(ring) : geometry.coordinates.map(polygon => polygon.map(ring));
    result[p.adm1_name][p.adm2_name] = { code: p.adm2_pcode, geometry, wards: {} };
}
for (const feature of wards.features) {
    const p = feature.properties;
    const district = result[p.adm1_name]?.[p.adm2_name];
    if (!district) throw new Error(`Unknown district for ward ${p.adm3_pcode}`);
    district.wards[`Ward ${p.adm3_name}`] = [p.center_lat, p.center_lon];
}
fs.mkdirSync(path.join('resources', 'data'), { recursive: true });
fs.writeFileSync(path.join('resources', 'data', 'zimbabwe.json'), JSON.stringify(result));
console.log(`Imported ${districts.features.length} districts and ${wards.features.length} wards.`);
