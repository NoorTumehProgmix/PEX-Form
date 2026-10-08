<p>{{ $body }}</p>

<p>View details <a href="{{ $link }}">here</a>.</p>

@if (isset($formData) && is_array($formData) && count($formData) > 0)
    <h3>Form Data:</h3>
    <table style="border-collapse: collapse; width: 100%;">
        <thead>
        <tr style="border: 1px solid #ddd; background-color: #f2f2f2;">
            <th style="border: 1px solid #ddd; padding: 8px;">Field</th>
            <th style="border: 1px solid #ddd; padding: 8px;">Value</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($formData as $field => $value)
            <tr style="border: 1px solid #ddd;">
                <td style="border: 1px solid #ddd; padding: 8px;">{{ ucfirst($field) }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">
                    {{ $value }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
