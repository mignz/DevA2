{% extends 'layout/main.volt' %}

{% block content %}
<h3>Control Panel &rsaquo; Add Host</h3>

<form action="/add" method="post" class="needs-validation" novalidate>
  <div class="form-group">
    <label for="domainname">Domain Name</label>
    <div class="input-group">
      <input type="text" class="form-control" name="domainname" id="domainname" placeholder="domain" pattern="[0-9A-Za-z\-.]{2,}" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" value="{{ post is defined ? post['domainname'] : null }}" autofocus required>
      <div class="input-group-append">
        <span class="input-group-text">.test</span>
      </div>
      <div class="invalid-feedback">
        Must be a valid domain name. Avoid adding ".test" at the end.
      </div>
    </div>
  </div>
  <div class="form-group">
    <label for="websiteconfig">Website Configuration</label>
    <select class="custom-select" name="websiteconfig" id="websiteconfig" required>
      <option value=""{% if post is not defined %} selected{% endif %}>Select a website configuration</option>
      <option value="html"{% if post is defined and post['websiteconfig'] is 'html' %} selected{% endif %}>Simple HTML website with rewriting</option>
      <option value="php"{% if post is defined and post['websiteconfig'] is 'php' %} selected{% endif %}>Typical PHP website with rewriting</option>
      <option value="phalcon"{% if post is defined and post['websiteconfig'] is 'phalcon' %} selected{% endif %}>Phalcon</option>
      <option value="custom"{% if post is defined and post['websiteconfig'] is 'custom' %} selected{% endif %}>Custom</option>
    </select>
    <div class="invalid-feedback">
      Choose one. "Typical PHP website with rewriting" usually works anywhere.
    </div>
  </div>
  <div class="form-group custom-config{% if post is not defined or post['websiteconfig'] is not 'custom' %} hidden{% endif %}">
    <label for="customconfig">Custom Nginx Configuration</label>
    <textarea autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" class="form-control" name="customconfig" id="customconfig" rows="12">{% if post is defined %}{{ post['customconfig'] }}{% else %}index index.php index.html index.htm;
location / {
    autoindex on;
    try_files $uri $uri/ /index.php?$args;
}
location ~ \.php$ {
    fastcgi_pass phpfpm;
    fastcgi_intercept_errors on;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
}{% endif %}</textarea>
  <small class="form-text text-muted">This configuration will be tested prior to restarting the web server. If it fails, the changes will be lost.</small>
  </div>
  <div class="form-group">
    <label for="websitepath">Website Path (Public directory)</label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text">/</span>
      </div>
      <input type="text" class="form-control" name="websitepath" id="websitepath" placeholder="path" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" value="{{ post is defined ? post['websitepath'] : null }}">
      <div class="invalid-feedback">
        Invalid path!
      </div>
    </div>
    <small class="form-text text-muted">Path to your website public directory starting from the websites path. If the directory does not exist it will be created.</small>
  </div>
  <div class="form-group">
    <div class="custom-control custom-checkbox">
      <input type="checkbox" class="custom-control-input" id="default" name="default">
      <label class="custom-control-label" for="default">Set as default</label>
      <i class="fas fa-question-circle" data-toggle="popover" data-content="The default virtual host is accessible through localhost. This means that you can open this website on other devices through your machine's local network IP adddress."></i>
    </div>
  </div>
  <button type="submit" class="btn btn-primary">Add</button> or <a href="/">Cancel</a>
</form>
{% endblock %}
