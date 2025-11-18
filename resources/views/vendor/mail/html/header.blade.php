@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === config('app.name'))
{{-- Custom LMS Header --}}
<table style="width: 100%; margin: 0 auto; text-align: center;">
    <tr>
        <td style="padding: 30px 0;">
            {{-- Logo/Brand --}}
            <h1 style="margin: 0; padding: 0; font-size: 36px; font-weight: bold; color: #4f46e5; letter-spacing: -0.5px;">
                📚 {{ config('app.name') }}
            </h1>
            {{-- Tagline --}}
            <p style="margin: 8px 0 0 0; padding: 0; font-size: 14px; color: #6b7280; font-weight: normal;">
                Learn Anything, Anytime, Anywhere
            </p>
            {{-- Decorative line --}}
            <div style="width: 60px; height: 3px; background: linear-gradient(to right, #4f46e5, #7c3aed); margin: 15px auto 0; border-radius: 2px;"></div>
        </td>
    </tr>
</table>
@else
{{ $slot }}
@endif
</a>
</td>
</tr>{{-- resources/views/vendor/mail/html/header.blade.php --}}
@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === config('app.name'))
{{-- Custom LMS Header --}}
<table style="width: 100%; margin: 0 auto; text-align: center;">
    <tr>
        <td style="padding: 30px 0;">
            {{-- Logo/Brand --}}
            <h1 style="margin: 0; padding: 0; font-size: 36px; font-weight: bold; color: #4f46e5; letter-spacing: -0.5px;">
                📚 {{ config('app.name') }}
            </h1>
            {{-- Tagline --}}
            <p style="margin: 8px 0 0 0; padding: 0; font-size: 14px; color: #6b7280; font-weight: normal;">
                Learn Anything, Anytime, Anywhere
            </p>
            {{-- Decorative line --}}
            <div style="width: 60px; height: 3px; background: linear-gradient(to right, #4f46e5, #7c3aed); margin: 15px auto 0; border-radius: 2px;"></div>
        </td>
    </tr>
</table>
@else
{{ $slot }}
@endif
</a>
</td>
</tr>