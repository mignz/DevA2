<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Dev&#923; 2 - Control Panel</title>
  <meta name="author" content="Miguel Nunes">
  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <link rel="stylesheet" href="/css/fontawesome/css/fontawesome-all.min.css">
  <link rel="stylesheet" href="/css/style.css">
</head>

<body>

<noscript>Javascript is required, please enable it!</noscript>

<div class="container">
  <nav class="navbar navbar-dark bg-dark">
    <a class="navbar-brand" href="/">
      <i class="fa fa-cog"></i>
      Dev&#923; 2
    </a>
  </nav>
  {% if error is defined %}
  <div class="alert alert-danger" role="alert">
    <h4 class="alert-heading">Woops!</h4>
    {% if error_output is defined and error_output %}
    <p>{{ error }}</p>
    <hr>
    <p class="mb-0"><pre>{{ error_output }}</pre></p>
    {% else %}
    <p class="mb-0">{{ error }}</p>
    {% endif %}
  </div>
  {% endif %}
  <div>
    {% block content %}{% endblock %}
  </div>
</div>

<div class="loading">
  <i class="fas fa-spinner fa-pulse"></i>
  Please wait<br>
  <small>This may take several minutes.
</div>

<script src="/js/jquery-3.3.1.min.js"></script>
<script src="/js/popper.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/devacp.js"></script>

</body>

</html>
