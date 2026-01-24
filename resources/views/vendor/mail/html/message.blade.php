<x-mail::layout>
    {{-- Body --}}
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="padding: 24px 0; background-color: #f8fafc;">
        <tr>
            <td align="center">
                <table width="570" cellpadding="0" cellspacing="0" role="presentation" style="border-radius: 16px; border: 1px solid #e5e7eb; background-color: #ffffff; box-shadow: 0 16px 32px rgba(15, 23, 42, 0.08);">
                    <tr>
                        <td style="padding: 32px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 1.6; color: #111827;">
                            {{ $slot }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-top: 24px;">
                <tr>
                    <td style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; line-height: 1.6; color: #6b7280;">
                        {{ $subcopy }}
                    </td>
                </tr>
            </table>
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        <x-mail::footer>
            © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
        </x-mail::footer>
    @endslot
</x-mail::layout>
