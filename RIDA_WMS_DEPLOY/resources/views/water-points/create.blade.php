@extends('layouts.app')

@section('title', 'Add New Water Point')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('water-points.index') }}">Water Points</a></li>
    <li class="breadcrumb-item active">Add New</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-plus me-2"></i>Add New Water Point</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('water-points.store') }}" method="POST">
                    @csrf
                    @include('water-points._form')
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('water-points.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Water Point
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection