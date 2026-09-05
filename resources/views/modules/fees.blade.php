@extends('layouts.app')

@section('title', 'Student Fee & Invoice Management')
@section('page_title', 'My Invoices & Fees')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Fees & Invoices</li>
@endsection

@section('content')
@php
    $isParent = Auth::check() && Auth::user()->role && Auth::user()->role->name === 'parent';
@endphp

<style>
    .fee-card {
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }
</style>

<div class="row g-4">
    <!-- Header Banner -->
    <div class="col-12">
        <div class="p-4 rounded-4 text-white shadow-sm" style="background: linear-gradient(135deg, #1c382b 0%, #2d5a45 100%);">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <span class="badge bg-white bg-opacity-20 text-white border border-white border-opacity-25 px-3 py-1 rounded-pill mb-2 fs-7">
                        <i class="bi bi-receipt me-1"></i> Fee Ledger & Invoices
                    </span>
                    <h3 class="fw-bold mb-1 text-white">Student Tuition & Fee Ledger</h3>
                    <p class="text-white-50 mb-0 small">
                        View monthly tuition challans, payment vouchers, ledger history, and download official receipts (PKR).
                    </p>
                </div>
                <div>
                    <span class="badge bg-success text-white px-3 py-2 rounded-pill fs-7 fw-bold shadow-sm">
                        <i class="bi bi-check-circle-fill me-1"></i> Account Status: Clear
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-success">
            <span class="text-muted small fw-bold text-uppercase">Current Month Status</span>
            <h4 class="fw-bold text-success mt-1 mb-0">PAID (PKR 15,000)</h4>
            <p class="text-muted fs-8 mb-0 mt-1">September 2026 Tuition & Materials</p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-primary">
            <span class="text-muted small fw-bold text-uppercase">Outstanding Balance</span>
            <h4 class="fw-bold text-primary mt-1 mb-0">PKR 0.00</h4>
            <p class="text-muted fs-8 mb-0 mt-1">No pending dues on account</p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-info">
            <span class="text-muted small fw-bold text-uppercase">Next Due Date</span>
            <h4 class="fw-bold text-info mt-1 mb-0">Oct 10, 2026</h4>
            <p class="text-muted fs-8 mb-0 mt-1">October 2026 Tuition Challan</p>
        </div>
    </div>

    <!-- Fee Invoices Table -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="bi bi-clock-history text-success me-2"></i> Official Fee Challans & Receipts
                </h5>
                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">Currency: PKR</span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead class="table-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th>Voucher #</th>
                            <th>Billing Period</th>
                            <th>Description</th>
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold text-dark">CHL-2026-09</td>
                            <td>September 2026</td>
                            <td>Monthly Tuition & Montessori Material Fee</td>
                            <td class="fw-bold text-dark">PKR 15,000</td>
                            <td class="text-success fw-bold">PKR 15,000</td>
                            <td>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold">
                                    <i class="bi bi-check-circle-fill me-1"></i> PAID
                                </span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-success rounded-pill px-3">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> Receipt PDF
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-dark">CHL-2026-08</td>
                            <td>August 2026</td>
                            <td>Monthly Tuition & Activity Fee</td>
                            <td class="fw-bold text-dark">PKR 15,000</td>
                            <td class="text-success fw-bold">PKR 15,000</td>
                            <td>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold">
                                    <i class="bi bi-check-circle-fill me-1"></i> PAID
                                </span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-success rounded-pill px-3">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> Receipt PDF
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
