@extends('layouts.app')

@section('title', 'Room Layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-th mr-2"></i> Room Layout: {{ $room->name }} ({{ $room->code }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">
                                        <i class="fas fa-building mr-1"></i> Block: {{ $room->block->name }}
                                        <span class="mx-2">|</span>
                                        <i class="fas fa-layer-group mr-1"></i> Floor: {{ $room->floor ?? 'N/A' }}
                                        <span class="mx-2">|</span>
                                        <i class="fas fa-users mr-1"></i> Capacity: {{ $room->capacity }} students
                                    </h6>
                                </div>
                                <div>
                                    <a href="{{ route('rooms.show', $room) }}" class="btn btn-info">
                                        <i class="fas fa-info-circle mr-1"></i> Room Details
                                    </a>
                                    <a href="{{ route('rooms.edit', $room) }}" class="btn btn-primary ml-2">
                                        <i class="fas fa-edit mr-1"></i> Edit Room
                                    </a>
                                    <a href="{{ route('rooms.index') }}" class="btn btn-secondary ml-2">
                                        <i class="fas fa-arrow-left mr-1"></i> Back to List
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(!$room->hasGridLayout())
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            No grid layout has been defined for this room. Please edit the room to add rows and columns.
                            <a href="{{ route('rooms.edit', $room) }}" class="btn btn-sm btn-primary ml-3">
                                <i class="fas fa-edit mr-1"></i> Define Layout
                            </a>
                        </div>
                    @else
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">Layout Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h2 class="mb-0">{{ $room->rows }}</h2>
                                                <p class="lead">Rows</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h2 class="mb-0">{{ $room->columns }}</h2>
                                                <p class="lead">Columns</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h2 class="mb-0">{{ $room->capacity }}</h2>
                                                <p class="lead">Total Seats</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Room Layout</h6>
                                    <div>
                                        <button class="btn btn-sm btn-light" id="print-layout">
                                            <i class="fas fa-print mr-1"></i> Print Layout
                                        </button>
                                        <div class="btn-group ml-2">
                                            <button class="btn btn-sm btn-light" id="zoom-in">
                                                <i class="fas fa-search-plus"></i>
                                            </button>
                                            <button class="btn btn-sm btn-light" id="zoom-out">
                                                <i class="fas fa-search-minus"></i>
                                            </button>
                                            <button class="btn btn-sm btn-light" id="zoom-reset">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <span class="badge badge-primary p-2">Front of Room (Blackboard/Projector)</span>
                                </div>
                                <div class="room-layout-container">
                                    <div class="room-layout" id="room-layout">
                                        <table class="table table-bordered room-grid">
                                            @for($row = 1; $row <= $room->rows; $row++)
                                                <tr>
                                                    @for($col = 1; $col <= $room->columns; $col++)
                                                        <td class="seat" data-row="{{ $row }}" data-col="{{ $col }}" data-seat="{{ (($row-1) * $room->columns) + $col }}">
                                                            <div class="seat-number">{{ (($row-1) * $room->columns) + $col }}</div>
                                                            <i class="fas fa-chair"></i>
                                                        </td>
                                                    @endfor
                                                </tr>
                                            @endfor
                                        </table>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <span class="badge badge-secondary p-2">Back of Room (Entrance)</span>
                                </div>
                                <div class="mt-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6>Legend</h6>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="seat-legend available">
                                                            <i class="fas fa-chair"></i>
                                                        </div>
                                                        <span class="ml-2">Available Seat</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="seat-legend occupied">
                                                            <i class="fas fa-chair"></i>
                                                        </div>
                                                        <span class="ml-2">Occupied Seat</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="seat-legend disabled">
                                                            <i class="fas fa-chair"></i>
                                                        </div>
                                                        <span class="ml-2">Disabled Seat</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="seat-legend special">
                                                            <i class="fas fa-chair"></i>
                                                        </div>
                                                        <span class="ml-2">Special Needs</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .room-layout-container {
        overflow-x: auto;
        max-width: 100%;
    }
    
    .room-layout {
        margin: 0 auto;
        transform-origin: center;
        transition: transform 0.3s;
    }
    
    .room-grid {
        margin: 0 auto;
        border-collapse: separate;
        border-spacing: 5px;
    }
    
    .seat {
        width: 40px;
        height: 40px;
        text-align: center;
        vertical-align: middle;
        background-color: #f8f9fa;
        position: relative;
        padding: 0;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .seat:hover {
        background-color: #e9ecef;
        transform: scale(1.1);
    }
    
    .seat-number {
        position: absolute;
        top: 2px;
        left: 2px;
        font-size: 10px;
        color: #6c757d;
    }
    
    .seat i {
        font-size: 18px;
        color: #3e92cc;
    }
    
    .seat.occupied i {
        color: #e74a3b;
    }
    
    .seat.disabled i {
        color: #6c757d;
    }
    
    .seat.special i {
        color: #f6c23e;
    }
    
    .seat-legend {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        border-radius: 4px;
        border: 1px solid #ced4da;
    }
    
    .seat-legend i {
        font-size: 16px;
    }
    
    .seat-legend.available i {
        color: #3e92cc;
    }
    
    .seat-legend.occupied i {
        color: #e74a3b;
    }
    
    .seat-legend.disabled i {
        color: #6c757d;
    }
    
    .seat-legend.special i {
        color: #f6c23e;
    }
    
    @media print {
        .card-header, .btn, .no-print {
            display: none !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .card-body {
            padding: 0 !important;
        }
        
        .room-layout {
            transform: scale(1) !important;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let scale = 1;
        const layout = document.getElementById('room-layout');
        
        // Zoom in
        $('#zoom-in').click(function() {
            scale += 0.1;
            layout.style.transform = `scale(${scale})`;
        });
        
        // Zoom out
        $('#zoom-out').click(function() {
            if (scale > 0.5) {
                scale -= 0.1;
                layout.style.transform = `scale(${scale})`;
            }
        });
        
        // Reset zoom
        $('#zoom-reset').click(function() {
            scale = 1;
            layout.style.transform = `scale(${scale})`;
        });
        
        // Print layout
        $('#print-layout').click(function() {
            window.print();
        });
        
        // Seat click event
        $('.seat').click(function() {
            const seatNumber = $(this).data('seat');
            const row = $(this).data('row');
            const col = $(this).data('col');
            
            alert(`Seat ${seatNumber} (Row ${row}, Column ${col}) clicked`);
            
            // Toggle seat status for demonstration
            $(this).toggleClass('occupied');
        });
    });
</script>
@endsection

