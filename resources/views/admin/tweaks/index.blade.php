@extends('adminlte::page')

@section('title', 'Stock Tweaks')

@section('content_header')
    <h1>Stock Tweaks (Inventory Adjustments)</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Recent Stock Tweaks</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered text-center align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>User</th>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Comments</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tweaks as $tweak)
                        <tr>
                            <td>{{ $tweak->tweak_id }}</td>
                            <td>{{ $tweak->created_at ? $tweak->created_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                            <td>{{ $tweak->user ? $tweak->user->name : 'N/A' }}</td>
                            <td>
                                @if($tweak->inventory && $tweak->inventory->product)
                                    <b>{{ $tweak->inventory->product->name }}</b>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($tweak->type == 'Input')
                                    <span class="badge badge-success">Input</span>
                                @else
                                    <span class="badge badge-danger">Output</span>
                                @endif
                            </td>
                            <td>
                                <b>{{ $tweak->quantity }}</b>
                                @if($tweak->inventory && $tweak->inventory->product)
                                    {{ $tweak->inventory->product->unit }}
                                @endif
                            </td>
                            <td>{{ $tweak->comments ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">No stock tweaks found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($tweaks->hasPages())
        <div class="card-footer d-flex justify-content-center">
            {{ $tweaks->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
@stop
