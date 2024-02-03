<!-- resources/views/pdf_template.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <style>
        /* Add your PDF styling here */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td,h1 {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        h3 {
            padding: 8px;
            text-align: center;
        }


        th {
            background-color: #f2f2f2;
        }
        .download-link {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
        }

        .download-link:hover {
            background-color: #45a049;
        }

    </style>
</head>
<body>
<h1>QR</h1>

<table>
    <thead>
    <tr>

        <th>link</th>
        <th>QR</th>
        <th>Download</th>


        <!-- Add more columns as needed -->
    </tr>
    </thead>
    <tbody>
    @foreach ( $links as $index=> $link)
        <tr>
            <td>{{$link['link']}}</td>
            <td>
                <img id="svg-{{$loop->iteration}}" width="150px" height="150px" src="{{ asset($link['QR']) }}" alt="QR">
            </td>
            <td>
                <a href="#" class="download-link" onclick="convertSvgToImage('svg-{{$loop->iteration}}','{{$index+1}}')">Download</a>
            </td>

            <!-- Add more columns as needed -->
        </tr>
    @endforeach
    </tbody>
</table>
<script>
    function convertSvgToImage(svgId,n) {
        const svgElement = document.getElementById(svgId);
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        const img = new Image();

        img.onload = function () {
            canvas.width = img.width;
            canvas.height = img.height;
            context.drawImage(img, 0, 0, img.width, img.height);
            canvas.toBlob(function (blob) {
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', n+ 'image.png'); // Change 'image.png' to 'image.jpg' for JPG format
                link.click();
                URL.revokeObjectURL(url);
            });
        };

        img.src = svgElement.src;
    }
</script>
</body>
</html>
