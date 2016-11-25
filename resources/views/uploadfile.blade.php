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
                	<form action="saveupload" method="post" enctype="multipart/form-data">
                	  <input type="hidden" name="_token" value="{{ csrf_token() }}">
					  <input type="file" name="fileimage" id="fileimage" accept="image/*">
					  <input type="text" name="name" id="name"  placeholder="Enter Output video name">
					  <input type="submit">
					</form>
                </div>
            </div>
        </div>
    </body>
</html>
