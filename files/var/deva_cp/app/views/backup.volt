{% extends 'layout/main.volt' %}

{% block content %}
<h3>Control Panel &rsaquo; Backup</h3>

<p><b>Backup complete!</b></p>

<p>The file "{{ file }}" is available in your websites directory.</p>

<p><a href="/" class="btn btn-primary">Go Back</a></p>
{% endblock %}
