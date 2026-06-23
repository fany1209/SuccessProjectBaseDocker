<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Pdf Document</title>
    <style>
        .table1 {
            border-collapse: collapse;
            width: 100%;
        }

        .table2Cells {
            border: 1px solid #555555;
            text-align: left;

            padding-left: 5px;
            padding-right: 5px;
        }

        .cells-1 {
            border: 1px solid #555555;
            text-align: center;
        }

        .text-uxs {
            font-size: 9px;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
        }

        .text-2uxs {
            font-size: 8px;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
        }

        .text-xs {
            font-size: 11px;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
        }

        .text-sm {
            font-size: 12px;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
        }

        .title {
            font-weight: bold;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 18px;
            margin: 0;
        }

        .container {
            float: left;
            width: 24.1%;
            margin-left: 7px;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<header>
    <table class="table1">
        <tbody>
            <tr>
                <td rowspan="2" style="width: 10%;" class="cells-1">
                    <img src="images/logo.png" width="144">
                </td>

                <td colspan="3" class="cells-1">
                    <p class="title">
                        REGISTRO DE TEMPERATURA Y HUMEDAD EN CEDIS
                    </p>
                </td>

                <td class="cells-1">
                    <p class="text-sm">
                        Código:<br>SSS-FOR-ALM-06
                    </p>
                </td>
            </tr>

            <tr>

                <td class="cells-1">
                    <p class="text-sm">
                        Fecha de elaboración:<br>30-Enero-2023
                    </p>
                </td>

                <td class="cells-1">
                    <p class="text-sm">
                        Fecha de actualización: --
                    </p>
                </td>

                <td class="cells-1" style="width: 11%;">
                    <p class="text-sm">
                        Versión: 00
                    </p>
                </td>

                <td class="cells-1">
                    <p class="text-sm">
                        Pág. {{ $page_number }} de {{ $total_pages }}
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <br>
</header>

<body>
    <div>
        @foreach ($res as $index => $week)
            <div class="container">
                <table class="table1">
                    <tbody>
                        <tr>
                            <td colspan="2" class="table2Cells">
                                <p class="text-xs" style="padding: 1px 0px 1px 0px">Semana: {{ $index }}</p>
                            </td>
                            <td colspan="2" class="table2Cells">
                                <p class="text-xs">Año: {{ $year }}</p>
                            </td>
                        </tr>

                        <tr>
                            <td class="cells-1">
                                <p class="text-xs" style="padding: 1px 0px 1px 0px">Dia</p>
                            </td>

                            <td class="cells-1">
                                <p class="text-xs">Horario</p>
                            </td>

                            <td class="cells-1">
                                <p class="text-xs">Temperatura</p>
                            </td>

                            <td class="cells-1">
                                <p class="text-xs">Humedad</p>
                            </td>
                        </tr>

                        @foreach ($week as $day)
                            <tr>
                                <td class="cells-1" rowspan="4">
                                    @switch($day['week_day'])
                                        @case(1)
                                            <p class="text-uxs">L</p>
                                            <p class="text-uxs">u</p>
                                            <p class="text-uxs">n</p>
                                            <p class="text-uxs">e</p>
                                            <p class="text-uxs">s</p>
                                        @break

                                        @case(2)
                                            <p class="text-uxs">M</p>
                                            <p class="text-uxs">a</p>
                                            <p class="text-uxs">r</p>
                                            <p class="text-uxs">t</p>
                                            <p class="text-uxs">e</p>
                                            <p class="text-uxs">s</p>
                                        @break

                                        @case(3)
                                            <p class="text-2uxs">M</p>
                                            <p class="text-2uxs">i</p>
                                            <p class="text-2uxs">e</p>
                                            <p class="text-2uxs">r</p>
                                            <p class="text-2uxs">c</p>
                                            <p class="text-2uxs">o</p>
                                            <p class="text-2uxs">l</p>
                                            <p class="text-2uxs">e</p>
                                            <p class="text-2uxs">s</p>
                                        @break

                                        @case(4)
                                            <p class="text-uxs">J</p>
                                            <p class="text-uxs">u</p>
                                            <p class="text-uxs">e</p>
                                            <p class="text-uxs">v</p>
                                            <p class="text-uxs">e</p>
                                            <p class="text-uxs">s</p>
                                        @break

                                        @case(5)
                                            <p class="text-uxs">V</p>
                                            <p class="text-uxs">i</p>
                                            <p class="text-uxs">e</p>
                                            <p class="text-uxs">r</p>
                                            <p class="text-uxs">n</p>
                                            <p class="text-uxs">e</p>
                                            <p class="text-uxs">s</p>
                                        @break

                                        @case(6)
                                            <p class="text-uxs">S</p>
                                            <p class="text-uxs">a</p>
                                            <p class="text-uxs">b</p>
                                            <p class="text-uxs">a</p>
                                            <p class="text-uxs">d</p>
                                            <p class="text-uxs">o</p>
                                        @break

                                        @default
                                    @endswitch
                                </td>

                                <td class="cells-1">
                                    <p class="text-xs">{{ $hours[0] }}</p>
                                </td>

                                @if (in_array(substr($hours[0], 0, 2), $day['missing_records']))
                                    <td class="cells-1">
                                        <p class="text-xs" style="color: red">Miss</p>
                                    </td>
                                    <td class="cells-1">
                                        <p class="text-xs" style="color: red">Miss</p>
                                    </td>
                                @else
                                    <td class="cells-1">
                                        <p class="text-xs">{{ $day['data'][0]['temperature'] }}</p>
                                    </td>
                                    <td class="cells-1">
                                        <p class="text-xs">{{ $day['data'][0]['humidity'] }}</p>
                                    </td>
                                @endif

                            </tr>

                            @for ($i = 1; $i < count($hours); $i++)
                                <tr>
                                    <td class="cells-1">
                                        <p class="text-xs">{{ $hours[$i] }}</p>
                                    </td>

                                    @if (in_array(substr($hours[$i], 0, 2), $day['missing_records']))
                                        @if ($day['week_day'] == 6 && $hours[$i] == '17:30')
                                            <td class="cells-1">
                                                <p class="text-xs">x</p>
                                            </td>
                                            <td class="cells-1">
                                                <p class="text-xs">x</p>
                                            </td>
                                        @else
                                            <td class="cells-1">
                                                <p class="text-xs" style="color: red">Miss</p>
                                            </td>
                                            <td class="cells-1">
                                                <p class="text-xs" style="color: red">Miss</p>
                                            </td>
                                        @endif
                                    @else
                                        @foreach ($day['data'] as $item)
                                            @if ($item['created_at']->format('H') == substr($hours[$i], 0, 2))
                                                <td class="cells-1">
                                                    <p class="text-xs">{{ $item['temperature'] }}</p>
                                                </td>
                                                <td class="cells-1">
                                                    <p class="text-xs">{{ $item['humidity'] }}</p>
                                                </td>
                                            @endif
                                        @endforeach
                                    @endif
                                </tr>
                            @endfor

                            <tr>
                                <td class="cells-1">
                                    <p class="text-xs"><b>Promedio</b></p>
                                </td>
                                <td class="cells-1">
                                    <p class="text-xs"><b>{{ $day['average_temperature'] }}</b></p>
                                </td>
                                <td class="cells-1">
                                    <p class="text-xs"><b>{{ $day['average_humidity'] }}</b></p>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>

                <br>
                <br>
                <br>
                <br>
                <div style="text-align: center">
                    <p class="text-xs">____________________________________</p>
                    <p class="text-xs">Nombre y firma de quien elaboro</p>
                </div>
            </div>

            @if (($loop->index + 1) % 4 == 0)
                @php
                    $page_number++;
                @endphp

                @if ($page_number <= $total_pages)
                    <div class="page-break"></div>
                    <table class="table1">
                        <tbody>
                            <tr>
                                <td rowspan="2" style="width: 10%;" class="cells-1">
                                    <img src="images/logo.png" width="144">
                                </td>

                                <td colspan="3" class="cells-1">
                                    <p class="title">
                                        REGISTRO DE TEMPERATURA Y HUMEDAD EN CEDIS
                                    </p>
                                </td>

                                <td class="cells-1">
                                    <p class="text-sm">
                                        Código:<br>SSS-FOR-ALM-06
                                    </p>
                                </td>
                            </tr>

                            <tr>

                                <td class="cells-1">
                                    <p class="text-sm">
                                        Fecha de elaboración:<br>30-Enero-2023
                                    </p>
                                </td>

                                <td class="cells-1">
                                    <p class="text-sm">
                                        Fecha de actualización: --
                                    </p>
                                </td>

                                <td class="cells-1" style="width: 11%;">
                                    <p class="text-sm">
                                        Versión: 00
                                    </p>
                                </td>

                                <td class="cells-1">
                                    <p class="text-sm">
                                        Pág. {{ $page_number }} de {{ $total_pages }}
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <br>
                @endif
            @endif
        @endforeach
    </div>
</body>

</html>
