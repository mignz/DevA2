{% extends 'layout/main.volt' %}

{% block content %}
<h3 class="float-left">Control Panel</h3>
<a href="/add" class="btn btn-dark float-right add-btn"><i class="fas fa-plus-circle"></i> Add</a>
<div class="clearfix"></div>

<div class="accordion" id="accordion">
  <div class="card">
    <div class="card-header" id="vhosts_header">
      <h5 class="mb-0">
        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#vhosts" aria-expanded="true" aria-controls="vhosts">
          Virtual Hosts
        </button>
      </h5>
    </div>
    <div id="vhosts" class="collapse show" aria-labelledby="vhosts_header" data-parent="#accordion">
      <div class="card-body">
        <table class="table table-hover">
          <thead class="thead-light">
            <tr>
              <th scope="col">Hostname</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            {% for file, host in hosts %}
            <tr{% if get['h'] is defined and get['h'] == host['domain'] %} class="highlighted"{% endif %}>
              {% if loop.first %}
              {% endif %}
              <th scope="row" width="200">
                <span data-toggle="tooltip" title="{{ file }}">
                  {% if host['default'] !== true and host['domain'] is 'localhost' %}
                    <s>{{ host['domain'] }}</s>
                  {% else %}
                    {{ host['domain'] }}
                  {% endif %}
                </span>
              </th>
              <td>
                <a href="https://{{ host['domain'] }}" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Open">
                  <i class="fas fa-globe"></i>
                </a>
                {% if host['domain'] == 'localhost' or host['domain'] == 'cp.test' %}
                <button class="btn btn-primary btn-sm" disabled>
                  <i class="fas fa-edit"></i>
                </button>
                {% else %}
                <a href="/edit/{{ host['domain'] }}" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Edit">
                  <i class="fas fa-edit"></i>
                </a>
                {% endif %}
                {% if host['default'] %}
                <button class="btn btn-success btn-sm" data-toggle="tooltip" title="Is Default" disabled>
                  <i class="fas fa-asterisk"></i>
                </button>
                {% else %}
                <a href="/default/{{ host['domain'] }}" class="btn btn-secondary btn-sm" data-toggle="tooltip" title="Set Default">
                  <i class="fas fa-asterisk"></i>
                </a>
                {% endif %}
                {% if host['domain'] == 'localhost' or host['domain'] == 'cp.test' or host['default'] %}
                <button class="btn btn-light btn-sm" data-toggle="tooltip" title="Remove" disabled>
                  <i class="fas fa-trash"></i>
                </button>
                {% else %}
                <a href="/delete/{{ host['domain'] }}" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Remove (No confirmation!)">
                  <i class="fas fa-trash"></i>
                </a>
                {% endif %}
              </td>
            </tr>
            {% endfor %}
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header" id="versions_header">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#versions" aria-expanded="false" aria-controls="versions">
          Versions
        </button>
      </h5>
    </div>
    <div id="versions" class="collapse" aria-labelledby="versions_header" data-parent="#accordion">
      <div class="card-body">
        <table class="table">
          <thead class="thead-light">
            <tr>
              <th scope="col"></th>
              <th scope="col">Actual Version</th>
              <th scope="col">Latest Version</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th scope="row" width="200">Dev&#923; 2</th>
              <td><span class="deva-version-actual">{{ ver_deva2 }}</span> with <i class="fab fa-docker" data-toggle="tooltip" title="Docker"></i></td>
              <td class="deva-version">&hellip;</td>
            </tr>
            <tr>
              <th scope="row" width="200">Alpine</th>
              <td>{{ ver_alpine }}</td>
              <td class="alpine-version">n/a</td>
            </tr>
            <tr>
              <th scope="row" width="200">Nginx</th>
              <td>{{ ver_nginx }}</td>
              <td class="nginx-version">n/a</td>
            </tr>
            <tr>
              <th scope="row" width="200">PHP</th>
              <td>{{ ver_php }}&nbsp;&nbsp;<a href="/phpinfo" data-toggle="tooltip" title="Show phpinfo()"><i class="fab fa-php"></i></a></td>
              <td class="php-version">&hellip;</td>
            </tr>
            <tr>
              <th scope="row" width="200">MariaDB</th>
              <td>{{ ver_db }}</td>
              <td class="mariadb-version">n/a</td>
            </tr>
            <tr>
              <th scope="row" width="200">Phalcon</th>
              <td class="phalcon-version-actual">{{ ver_phalcon }}</td>
              <td class="phalcon-version">&hellip;</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header" id="services_header">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#services" aria-expanded="false" aria-controls="services">
          Services
        </button>
      </h5>
    </div>
    <div id="services" class="collapse" aria-labelledby="services_header" data-parent="#accordion">
      <div class="card-body">
        <table class="table">
          <thead class="thead-light">
            <tr>
              <th scope="col">Service</th>
              <th scope="col">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th scope="row" width="200">Nginx</th>
              <td>
                <span class="badge badge-success">RUNNING</span>&nbsp;&nbsp;<small>
                  <a href="#" class="restart-service" service="nginx" data-toggle="tooltip" title="Restart Nginx"><i class="fas fa-sync-alt"></i></a>
                </small>
              </td>
            </tr>
            <tr>
              <th scope="row" width="200">PHP-FPM</th>
              <td>
                <span class="badge badge-success">RUNNING</span>&nbsp;&nbsp;<small>
                  <a href="#" class="restart-service" service="php-fpm" data-toggle="tooltip" title="Restart PHP-FPM"><i class="fas fa-sync-alt"></i></a>
                </small>
              </td>
            </tr>
            <tr>
              <th scope="row" width="200">MySQL</th>
              <td>
                <span class="badge badge-{{ sql_badge }}">{{ sql_status }}</span>&nbsp;&nbsp;<small>
                  <a href="#" class="restart-service" service="mysql" data-toggle="tooltip" title="Restart MySQL (MariaDB)"><i class="fas fa-sync-alt"></i></a>
                </small>
              </td>
            </tr>
            <tr>
              <th scope="row" width="200">Redis</th>
              <td>
                <span class="badge badge-{{ redis_badge }}">{{ redis_status }}</span>&nbsp;&nbsp;<small>
                  <a href="#" class="restart-service" service="redis" data-toggle="tooltip" title="Start/Restart Redis"><i class="fas fa-sync-alt"></i></a>
                </small>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header" id="mailer_header">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#mailer" aria-expanded="false" aria-controls="mailer">
          PHP Mailer (SMTP)
        </button>
      </h5>
    </div>
    <div id="mailer" class="collapse" aria-labelledby="mailer_header" data-parent="#accordion">
      <div class="card-body">
        <form action="/smtp" method="post" class="needs-validation" novalidate>
          <div class="form-group">
            <label for="server">SMTP Server</label>
            <input name="server" type="text" class="form-control" id="server" value="{{ smtp['server'] }}" pattern="[a-zA-Z0-9\-_.]{3,}" required placeholder="mail.domain.com">
            <div class="invalid-feedback">
              You must specify an SMTP server!
            </div>
          </div>
          <div class="form-group">
            <label for="port">SMTP Server Port</label>
            <input name="port" type="number" class="form-control" id="port" value="{{ smtp['port'] }}" required placeholder="25">
            <div class="invalid-feedback">
              You must specify an SMTP server numeric port!
            </div>
          </div>
          <div class="form-group">
            <label for="user">SMTP Username</label>
            <input name="user" type="text" class="form-control" id="user" value="{{ smtp['user'] }}" pattern="[a-zA-Z0-9\-_.@]{3,}" required placeholder="address@domain.com">
            <div class="invalid-feedback">
              You must specify an SMTP username (usually an email address)!
            </div>
          </div>
          <div class="form-group">
            <label for="pass">SMTP Password</label>
            <input name="pass" type="password" class="form-control" id="pass" value="{{ smtp['pass'] }}" placeholder="Password">
            <div class="invalid-feedback">
              You must specify the SMTP password!
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Apply</button>
          <input type="button" class="btn btn-secondary" data-toggle="tooltip" title="Send a test email" onclick="testEmail()" value="Test">
        </form>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header" id="backup_header">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#backup" aria-expanded="false" aria-controls="backup">
          Backup/Restore
        </button>
      </h5>
    </div>
    <div id="backup" class="collapse" aria-labelledby="backup_header" data-parent="#accordion">
      <div class="card-body">
        <form action="/restore" method="post" class="needs-validation" novalidate enctype="multipart/form-data">
          <p><a href="/backup" class="btn btn-primary" onclick="this.innerHTML='Please wait...';">Create Backup</a></p>
          <p>What's included:</p>
          <p>
            <ul>
              <li>Website files</li>
              <li>Databases</li>
              <li>Virtual Hosts (Config)</li>
            </ul>
          </p>
          <hr>
          <div class="form-group">
            <label for="user">Restore</label>
            <div class="custom-file">
              <input type="file" class="custom-file-input" id="backupfile" name="backupfile" required>
              <label class="custom-file-label" for="backupfile">Choose a backup file</label>
            </div>
          </div>
          <button type="submit" class="btn btn-primary btn-sm">Restore</button>
          <br><br>
          <small><b>Warning:</b> Restoring a previous backup will replace any existing conflicting files! For best results, make sure this is a clean installation.</small>
        </form>
      </div>
    </div>
  </div>
</div>
{% endblock %}
