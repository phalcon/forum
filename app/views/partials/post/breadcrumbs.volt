<ol class="breadcrumb">
    <li>{{ link_to('', 'Home') }}</li>
    <li>{{ link_to('category/' ~ post.category.id ~ '/' ~ post.category.slug, post.category.name) }}</li>
</ol>
