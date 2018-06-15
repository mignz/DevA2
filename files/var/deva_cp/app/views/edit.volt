{% extends 'layout/main.volt' %}

{% block content %}
<h3>Control Panel &rsaquo; Edit <u>{{ domain }}</u></h3>

<form action="/edit/{{ domain }}" method="post" class="needs-validation" novalidate>
  <div class="form-group">
    <label for="domainname">Domain Name</label>
    <div class="input-group">
      <input type="text" class="form-control" name="domainname" id="domainname" placeholder="domain" pattern="[0-9A-Za-z\-.]{2,}" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" value="{{ hostfield }}" required>
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
      <option value="">Select a website configuration</option>
      <option value="html"{% if config is 'html' %} selected{% endif %}>Simple HTML website with rewriting</option>
      <option value="php"{% if config is 'php' %} selected{% endif %}>Typical PHP website with rewriting</option>
      <option value="phalcon"{% if config is 'phalcon' %} selected{% endif %}>Phalcon</option>
      <option value="custom"{% if config is 'custom' %} selected{% endif %}>Custom</option>
    </select>
    <div class="invalid-feedback">
      Choose one. "Typical PHP website with rewriting" usually works anywhere.
    </div>
  </div>
  <div class="form-group custom-config{% if config is not 'custom' %} hidden{% endif %}">
    <label for="customconfig">Custom Nginx Configuration</label>
    <textarea autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" class="form-control" name="customconfig" id="customconfig" rows="12">{{ custom }}</textarea>
  <small class="form-text text-muted">This configuration will be tested prior to restarting the web server. If it fails, the changes will be lost.</small>
  </div>
  <div class="form-group">
    <label for="websitepath">Website Path (Public directory)</label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text">/</span>
      </div>
      <input type="text" class="form-control" name="websitepath" id="websitepath" placeholder="path" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" value="{{ path }}">
      <div class="invalid-feedback">
        Invalid path!
      </div>
    </div>
    <small class="form-text text-muted">Path to your website public directory starting from the websites path. If the directory does not exist it will be created.</small>
  </div>
  <button type="submit" class="btn btn-primary">Edit</button> or <a href="/">Cancel</a>
</form>
{% endblock %}
