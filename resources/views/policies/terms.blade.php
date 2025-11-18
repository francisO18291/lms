@extends('layouts.app')

@section('title', 'Terms of Service')

@section('content')
<style>
    .policy-header {
        background: linear-gradient(135deg, #198754, #0d6efd);
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
        color: #198754;
        margin-top: 1.5rem;
    }
</style>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="policy-header mb-4">
        <h1 style="font-size: 32px;">Terms of Service</h1>
        <p>By using our platform, you agree to these terms.</p>
    </div>

    <div class="policy-section">
        <p>These Terms of Service govern the use of our LMS platform and its features.</p>

        <h3 style="font-size: 19px;">1. Use of Service</h3>
        <p>Our LMS is intended for educational purposes. Users must act responsibly and comply with all laws.</p>

        <h3 style="font-size: 19px;">2. User Responsibilities</h3>
        <p>Maintain confidentiality of your account credentials and report unauthorized access immediately.</p>

        <h3 style="font-size: 19px;">3. Limitation of Liability</h3>
        <p>We are not responsible for data loss, unauthorized access, or service interruptions beyond our control.</p>
    </div>
</div>
@endsection
