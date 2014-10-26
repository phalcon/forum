<div class="clearfix">
	<div class="col-lg-9  center-block">
		<div class="panel panel-default">
		  <div class="panel-heading">Categories</div>
			
				 
	 <table class="table">
        <thead>
          <tr>
            <th>#</th>
            <th class="col-lg-9">Category Name</th>
            <th></th>
            <th>Last Message</th>
          </tr>
        </thead>
        <tbody>
		
		{%- for categorie in categories -%}
          <tr>
            <td>{{ categorie.id }}</td>
            <td>{{ link_to('category/' ~ categorie.id ~ '/' ~ categorie.slug, categorie.name) }}
			<br><small>{{ categorie.description }}</small></td>
            <td><?php echo count(\Phosphorum\Models\Posts::find("categories_id=".$categorie->id)); ?> Theards</td>
            <td><?php
			
			if(count(\Phosphorum\Models\Posts::find("categories_id=".$categorie->id)) > 0) {
            $last_author = $this
            ->modelsManager
            ->createBuilder()
            ->from(array('p' => 'Phosphorum\Models\Posts'))
			->where('p.categories_id = "'.$categorie->id.'"')
			->join('Phosphorum\Models\Users', "u.id = p.users_id", 'u')
			->columns(array('p.users_id as users_id','u.name as name_user','p.title as post1_title','p.slug as post1_slug','p.id as post1_id'))
			->orderBy('p.id DESC')
			->limit(1)
			->getQuery()
			->execute();
			}
			
				if (count(\Phosphorum\Models\Posts::find("categories_id=".$categorie->id)) > 0) {
				echo $this->tag->linkTo("discussion/{$last_author[0]->post1_id}/{$last_author[0]->post1_slug}", $last_author[0]->post1_title);
					echo '<br>@'.$last_author[0]->name_user;
				} else {
					echo '---';	
				}
			
			?></td>
          </tr>
		{%- endfor -%}
      
        </tbody>
      </table>
			
		</div>
	</div>
</div>