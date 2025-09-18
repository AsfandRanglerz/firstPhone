@foreach ($topProducts as $index => $p)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $p->brand_name }} - {{ $p->model_name }}</td>
        <td>{{ $p->vendor_name }}</td>
        <td>{{ $p->qty }}</td>
        <td>Rs {{ number_format($p->revenue) }}</td>
    </tr>
@endforeach
