@extends('layouts.app')

@section('title', $title ?? 'Module Foundation')
@section('page_title', $title ?? 'Module Foundation')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ $title ?? 'Module' }}</li>
@endsection

@section('content')
@php
    $isParent = Auth::check() && Auth::user()->role && Auth::user()->role->name === 'parent';
@endphp
<div class="row">
    <div class="col-12">
        <x-card>
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                <div>
                    <h3 class="m-card-title fs-4">{{ $title ?? 'Module Foundation' }}</h3>
                    <p class="m-card-subtitle">
                        @if($isParent)
                            Child Portal View • Displaying verified reports and records created by your child's Lead Directress and Campus Administration.
                        @else
                            {{ $subtitle ?? 'Navigation foundation prepared for step-by-step module implementation.' }}
                        @endif
                    </p>
                </div>
                <x-badge variant="sage" class="fs-6 px-3 py-2">
                    {{ $isParent ? 'Child Read-Only View' : 'Module Foundation Established' }}
                </x-badge>
            </div>

            <!-- Search & Filters Container -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="input-group" style="width: 280px;">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0" placeholder="Search {{ strtolower($title ?? 'records') }}..." disabled>
                    </div>
                    <select class="form-select" style="width: 160px;" disabled>
                        <option>All Statuses</option>
                    </select>
                </div>

                @if(!$isParent)
                <div>
                    <x-button variant="primary" icon="bi bi-plus-lg" disabled>
                        Add {{ $title ?? 'Record' }}
                    </x-button>
                </div>
                @else
                <div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                        <i class="bi bi-shield-check me-1"></i> Verified Directress & Campus Records
                    </span>
                </div>
                @endif
            </div>

            <x-empty-state 
                icon="{{ $icon ?? 'bi bi-grid-3x3-gap-fill' }}" 
                title="{{ $isParent ? 'No ' . ($title ?? 'Records') . ' Found for Child' : 'No ' . ($title ?? 'Records') . ' Found' }}" 
                description="{{ $isParent ? 'Your child does not have any recorded entries in this section yet. Official reports and entries submitted by the Lead Directress will automatically appear here.' : 'This module\'s UI layout and routing foundation are ready. Real database schema and business logic will be bound in subsequent development steps without dummy data.' }}"
                actionText="{{ $isParent ? 'Back to Parent Dashboard' : 'Back to Dashboard' }}"
                actionUrl="{{ route('dashboard') }}"
                actionIcon="bi bi-speedometer2"
            />
        </x-card>
    </div>
</div>
@endsection
