@props(['url'])
<tr>
<td>
<table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="content-cell" align="center">
    {{-- Social Links (Optional) --}}
    <table style="margin: 20px auto 15px; text-align: center;" cellpadding="0" cellspacing="0">
        <tr>
            <td style="padding: 0 8px;">
                <a href="#" style="color: #4f46e5; text-decoration: none; font-size: 20px;">
                    📘
                </a>
            </td>
            <td style="padding: 0 8px;">
                <a href="#" style="color: #4f46e5; text-decoration: none; font-size: 20px;">
                    🐦
                </a>
            </td>
            <td style="padding: 0 8px;">
                <a href="#" style="color: #4f46e5; text-decoration: none; font-size: 20px;">
                    📸
                </a>
            </td>
            <td style="padding: 0 8px;">
                <a href="#" style="color: #4f46e5; text-decoration: none; font-size: 20px;">
                    💼
                </a>
            </td>
        </tr>
    </table>

    {{-- Quick Links --}}
    <p style="color: #6b7280; font-size: 13px; line-height: 1.6; margin: 0 0 12px 0; padding: 0;">
        <a href="{{ url('/') }}" style="color: #4f46e5; text-decoration: none; font-weight: 500;">Home</a>
        <span style="color: #d1d5db; margin: 0 8px;">•</span>
        <a href="{{ url('/courses') }}" style="color: #4f46e5; text-decoration: none; font-weight: 500;">Courses</a>
        <span style="color: #d1d5db; margin: 0 8px;">•</span>
        <a href="{{ url('/categories') }}" style="color: #4f46e5; text-decoration: none; font-weight: 500;">Categories</a>
        <span style="color: #d1d5db; margin: 0 8px;">•</span>
        <a href="#" style="color: #4f46e5; text-decoration: none; font-weight: 500;">Support</a>
    </p>

    {{-- Copyright --}}
    <p style="color: #9ca3af; font-size: 12px; line-height: 1.5; margin: 15px 0 5px 0; padding: 0;">
        © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </p>

    {{-- Address (Optional) 
    <p style="color: #9ca3af; font-size: 11px; line-height: 1.4; margin: 5px 0 0 0; padding: 0;">
        123 Learning Street, Education City, ED 12345
    </p>--}}

    {{-- Unsubscribe link --}}
    <p style="color: #9ca3af; font-size: 11px; line-height: 1.4; margin: 12px 0 0 0; padding: 0;">
        Don't want to receive these emails? 
        <a href="#" style="color: #6b7280; text-decoration: underline;">Unsubscribe</a>
    </p>

    {{ Illuminate\Mail\Markdown::parse($slot) }}
</td>
</tr>
</table>
</td>
</tr>