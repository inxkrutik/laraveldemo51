<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">Upload File 5</div>
                <div>
                    <form action="thumbnail" method="post" enctype="multipart/form-data">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      <input type="file" name="image" id="image" accept="image/*">
                      <input type="submit">
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>