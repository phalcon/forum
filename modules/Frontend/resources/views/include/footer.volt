<footer>
    <div class="container">
        <div class="row justify-content-center">
            {{- partial('partials/footer-statistics') -}}

            <div class="clearfix"></div>
            <div class="col-lg-12 col-md-12 col-sm-12 powerby">
                <p>{{ application_short_description }}. Powered by Phosphorum v{{ forum_version() }}</p>
            </div>
        </div>
    </div>
</footer>
