@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row row-deck row-cards">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Total Products</div>
                </div>
                <div class="h1 mb-3">0</div>
                <div class="d-flex mb-2">
                    <div>Inventory module</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Total Invoices</div>
                </div>
                <div class="h1 mb-3">0</div>
                <div class="d-flex mb-2">
                    <div>Accounting module</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection