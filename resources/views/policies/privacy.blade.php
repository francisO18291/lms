@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
<style>
    .policy-header {
        background: linear-gradient(135deg, #007bff, #6610f2);
        color: white;
        padding: 3rem 1rem;
        border-radius: 10px;
        text-align: center;
    }
    .policy-section {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        padding: 2rem;
        margin-top: -2rem;
    }
    .policy-section h3 {
        color: #007bff;
        margin-top: 1.5rem;
    }
</style>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="policy-header mb-4">
        <h1 style="font-size: 32px;">Privacy Policy</h1>
        <p>We value your privacy and data protection.</p>
    </div>

    <div class="policy-section">
        <p>Our Privacy Policy explains how we collect, use, and protect your information when using our LMS platform.</p>

        <h3 style="font-size: 19px;">1. Information We Collect</h3>
        <p>We collect user details such as your name, email, and activity data to improve the system’s performance and your learning experience.</p>

        <h3 style="font-size: 19px;">2. How We Use Information</h3>
        <p>Data is used to provide better support, communication, and security services.</p>

        <h3 style="font-size: 19px;">3. Data Protection</h3>
        <p>Your data is encrypted and stored securely. We never share your data with third parties without consent.</p>
    </div>
</div>
@endsection
