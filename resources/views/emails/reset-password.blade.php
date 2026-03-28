<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
    <table role="presentation" cellspacing="0" cellpadding="0" width="100%" style="background-color: #f3f4f6; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table role="presentation" cellspacing="0" cellpadding="0" width="560" style="max-width: 560px; width: 100%;">
                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding-bottom: 32px;">
                            <span style="font-size: 24px; font-weight: 900; color: #4f46e5; letter-spacing: -0.5px;">{{ config('app.name') }}</span>
                        </td>
                    </tr>

                    <!-- Main Card -->
                    <tr>
                        <td style="background-color: #ffffff; border-radius: 16px; padding: 48px 40px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                            <!-- Icon -->
                            <table role="presentation" cellspacing="0" cellpadding="0" width="100%">
                                <tr>
                                    <td align="center" style="padding-bottom: 24px;">
                                        <div style="width: 64px; height: 64px; background-color: #eef2ff; border-radius: 16px; display: inline-flex; align-items: center; justify-content: center;">
                                            <span style="font-size: 28px;">🔑</span>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <h1 style="margin: 0 0 8px 0; font-size: 24px; font-weight: 800; color: #111827; text-align: center;">Reset Your Password</h1>
                            <p style="margin: 0 0 32px 0; font-size: 15px; color: #6b7280; text-align: center; line-height: 1.6;">
                                Hi {{ $user->name }}, we received a request to reset your password. Click the button below to create a new one:
                            </p>

                            <!-- CTA Button -->
                            <table role="presentation" cellspacing="0" cellpadding="0" width="100%">
                                <tr>
                                    <td align="center" style="padding-bottom: 32px;">
                                        <a href="{{ $resetUrl }}" style="display: inline-block; padding: 14px 40px; background-color: #4f46e5; color: #ffffff; text-decoration: none; font-weight: 700; font-size: 15px; border-radius: 12px; box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);">
                                            Reset Password
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Expiry Note -->
                            <div style="background-color: #fef3c7; border: 1px solid #fde68a; border-radius: 12px; padding: 16px; margin-bottom: 24px;">
                                <p style="margin: 0; font-size: 13px; color: #92400e; text-align: center; font-weight: 500;">
                                    ⏰ This link will expire in <strong>{{ $expireMinutes }} minutes</strong>.
                                </p>
                            </div>

                            <!-- Fallback URL -->
                            <p style="margin: 0 0 8px 0; font-size: 13px; color: #9ca3af; text-align: center;">
                                If the button doesn't work, copy and paste this URL into your browser:
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #6366f1; text-align: center; word-break: break-all; line-height: 1.6;">
                                {{ $resetUrl }}
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding-top: 32px; text-align: center;">
                            <p style="margin: 0 0 8px 0; font-size: 13px; color: #9ca3af;">
                                If you didn't request a password reset, you can safely ignore this email.
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #d1d5db;">
                                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
