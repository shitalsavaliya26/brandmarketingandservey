<!DOCTYPE html>
<html>
<head>
    <title>Win Images</title>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }

        table.center {
            margin-left: auto;
            margin-right: auto;
        }

       .page-break{
            page-break-after: always
        }
        .page-break-avoid{
            page-break-inside: avoid        
        }
    </style>
</head>
<body>
    <!-- <h4 class="card-title" style="text-align: center;"> Win Images</h4> -->
    <table class="center">
        <?php $i = 1; ?>
        @foreach($notificationTopicImages as $rows)
            <tr>
                <th style="width: 600px;">
                    <img src="{{$rows['image'] }}" onerror="this.onerror=null;this.src='{{asset("backend/images/no-found.png")}}'" style="width: 100%; ">
                    @if(count($notificationTopicImages) > $i)
                    <div class="page-break"></div>
                    @else
                    <div class="page-break-avoid"></div>
                    @endif
                </th>
            </tr>
            <?php $i++; ?>
        @endforeach
    </table>
</body>
</html>
