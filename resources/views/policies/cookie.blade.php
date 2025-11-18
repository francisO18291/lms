@extends('layouts.app')

@section('title', 'Cookie Policy')

@section('content')
<style>
    .policy-header {
        background: linear-gradient(135deg, #fd7e14, #ffc107);
        color: #212529;
        padding: 3rem 1rem;
        border-radius: 10px;
        text-align: center;
        font-weight: 500;
    }
    .policy-section {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        padding: 2rem;
        margin-top: -2rem;
    }
    .policy-section h3 {
        color: #fd7e14;
        margin-top: 1.5rem;
    }
</style>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="policy-header mb-4">
        <h1 style="font-size: 32px;">Cookie Policy</h1>
        <p>We use cookies to make your experience smoother and more personalized.</p>
    </div>

    <div class="policy-section">
        <p>Cookies are small files that help us recognize you and remember your preferences when you visit our platform.</p>

        <h3 style="font-size: 19px;">1. What Are Cookies?</h3>
        <p>Cookies store small data pieces on your browser to identify returning users and preferences.</p>

        <h3 style="font-size: 19px;">2. How We Use Cookies</h3>
        <p>We use cookies to remember login sessions, track site usage, and analyze performance.</p>

        <h3 style="font-size: 19px;">3. Managing Cookies</h3>
        <p>You can disable cookies in your browser settings, but some LMS features may stop working properly.</p>
    </div>
</div>
@endsection
