{% extends Default.php %}

{% block title %} Sprint your code -  Build your web applications with less code and fast time {% endblock %}

{% block content %}
<div class="jumbotron text-center rounded-0 mb-0">
    <h1 class="display-3 text-danger">Opps sparkle <i class="fa fa-smile"></i> page not found!</h1>
    <p class="lead">{{ $message; }}</p>
    <p class="lead">
        <a class="btn btn-primary btn-lg" href="#" role="button">See documentation</a>
    </p>
</div>   
{% endblock %}