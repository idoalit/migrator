<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Pemindah</title>

    <!-- Bootstrap -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="jumbotron">
      <div class="container">
        <h1>Pemindah data</h1>
      </div>
    </div>
    <div class="container">
      <form class="form" id="form-pemindah">
        <div class="page-header">
          <h2>Konfigurasi database</h2>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="page-header">
              <h3>Dari</h3>
            </div>
            <div class="form-group">
              <label for="host-a">Host</label>
              <input type="text" name="host-a" value="localhost" placeholder="Database Host" class="form-control">
            </div>
            <div class="form-group">
              <label for="port-a">Port</label>
              <input type="text" name="port-a" value="3306" placeholder="Database port" class="form-control">
            </div>
            <div class="form-group">
              <label for="name-a">Database Name</label>
              <input type="text" name="name-a" placeholder="Database name" class="form-control">
            </div>
            <div class="form-group">
              <label for="name-a">User Name</label>
              <input type="text" name="user-a" placeholder="Username" class="form-control">
            </div>
            <div class="form-group">
              <label for="password-a">Password</label>
              <input type="text" name="pass-a" placeholder="Database password" class="form-control">
            </div>
          </div>
          <div class="col-md-6">
            <div class="page-header">
              <h3>Ke</h3>
            </div>
            <div class="form-group">
              <label for="host-a">Host</label>
              <input type="text" name="host-b" value="localhost" placeholder="Database Host" class="form-control">
            </div>
            <div class="form-group">
              <label for="port-a">Port</label>
              <input type="text" name="port-b" value="3306" placeholder="Database port" class="form-control">
            </div>
            <div class="form-group">
              <label for="name-a">Database Name</label>
              <input type="text" name="name-b" placeholder="Database name" class="form-control">
            </div>
            <div class="form-group">
              <label for="name-a">User Name</label>
              <input type="text" name="user-b" placeholder="Username" class="form-control">
            </div>
            <div class="form-group">
              <label for="password-a">Password</label>
              <input type="text" name="pass-b" placeholder="Database password" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="page-header">
            <h2>Pilih data yang akan dipindah</h2>
          </div>
          <div class="col-md-6">
            <div class="checkbox">
              <label>
                <input type="checkbox" name="data[]" value="bibliography"> Bibliografi
              </label>
            </div>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="data[]" value="member"> Keanggotaan
              </label>
            </div>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="data[]" value="loan"> Sirkulasi
              </label>
            </div>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="data[]" value="system"> Sistem
              </label>
            </div>
            <button type="submit" name="pindahkan" class="btn btn-primary">Pindahkan sekarang</button>
          </div>
        </div>
      </form>
    </div>

    <hr>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script type="text/javascript">
      'use script';
      $(document).ready(function () {
        var formPemisah = $('#form-pemindah')
        formPemisah.submit(function (e) {
          e.preventDefault()
          $.ajax({
            method: 'GET',
            url: 'pemindah.action.php',
            data: formPemisah.serialize()
          })
          .done(function (msg) {
            alert(msg)
          })
        })
      })
    </script>
  </body>
</html>
