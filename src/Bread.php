<?php

  namespace Jinx;

  class Bread
  {
    
    protected $args;
    protected $crumbs = [];
    
    public function __construct(array $args = [])
    {
      
      $this->args = apply_filters('jinx_breadcrumbs', array_merge([
        'home' => __('Home', 'jinx-breadcrumbs'),
        'search' => __('Search: "%s"', 'jinx-breadcrumbs'),
        '404' => __('Error 404', 'jinx-breadcrumbs'),
        'author' => __('Author: %s', 'jinx-breadcrumbs'),
        'year' => 'Y',
        'month' => 'F',
        'day' => 'd',
        'before' => '<nav aria-label="breadcrumb"><ol>',
        'after' => '</ol></nav>',
        'before_item' => '<li%s>',
        'after_item' => '</li>',
      ], $args));
      
      $this->generate();
      
    }
    
    public function getCrumbs() : array
    {
      return $this->crumbs;
    }
    
    public function addCrumb(array $attr, $index = null)
    {
      
      $crumb = new Crumb($attr);
      
      if (isset($index)) {
        array_splice($this->crumbs, $index, 0, [$crumb]);
      } else {
        $this->crumbs[] = $crumb;
      }
      
      return $this; 
      
    }
    
    protected function generate()
    {
      
      global $post;
      
      $this->addHome();
      
      if (!is_front_page()) {

        if (is_page() || is_attachment() || (is_single() && $post->post_type !== 'post')) {
                    
          $this->addPostType($post->post_type);

        } elseif (is_404()) {

          $this->addCrumb([
            'type' => '404',
            'title' => $this->args['404']
          ]);

        } elseif (is_search()) {

          $this->addCrumb([
            'type' => 'search',
            'title' => sprintf($this->args['search'], get_search_query())
          ]);

        } elseif (is_home() || is_category() || is_tag() || is_date() || is_author()) {

          $this->addBlog(is_home());

          if (is_single()) {

            $this->addPostType($post->post_type);

          } elseif (is_category() || is_tag()) {

            $this->addTerm();

          } elseif (is_date()) {

            $this->addDate();

          } elseif (is_author()) {
            
            $this->addAuthor();
            
          }

        } elseif (is_tax()) {
          
          $this->addTerm();
          
        } else {
          
          $this->addBlog();
          
          $this->addPostType($post->post_type);
          
        }
      
      }
      
    }
    
    protected function addHome()
    {
      
      $pageOnFront = intval(get_option('page_on_front'));
      
      if (empty($pageOnFront)) {
        
        $data = [
          'type' => 'home',
          'title' => $this->args['home']
        ];
        
      } else {
        
        $data = [
          'type' => 'post_type',
          'post_type' => 'page',
          'pid' => $pageOnFront,
          'title' => get_the_title($pageOnFront)
        ];
          
      }

      $this->addCrumb(array_merge($data, [
        'url' => get_home_url()
      ]));
      
    }
    
    protected function addBlog($last = false)
    {
      
      $pageForPosts = intval(get_option('page_for_posts'));
      if (!empty($pageForPosts)) {
        
        $this->addCrumb([
          'type' => 'post_type',
          'post_type' => 'page',
          'page_id' => $pageForPosts,
          'title' => get_the_title($pageForPosts),
          'url' => $last ? null : get_permalink($pageForPosts),
        ]);
        
      }
      
    }
    
    protected function addTerm()
    {
      
      global $wp_query;
      $term = $wp_query->get_queried_object();

      $this->addArchive(get_taxonomy($term->taxonomy));

      if (!empty($term->parent)) {

        $ancestors = array_reverse(get_ancestors($term->term_id, $term->taxonomy, 'taxonomy'));
        
        foreach ($ancestors as $ancestor) {
          
          $ancestor = get_term($ancestor, $term->taxonomy);
          
          $this->addCrumb([
            'type' => 'taxonomy',
            'taxonomy' => $ancestor->taxonomy,
            'term_id' => $ancestor->term_id,
            'title' => apply_filters('single_term_title', $ancestor->name),
            'url' => get_term_link($ancestor->term_id, $ancestor->taxonomy)
          ]);
          
        }
        
      }

      $this->addCrumb([
        'type' => 'taxonomy',
        'taxonomy' => $term->taxonomy,
        'term_id' => $term->term_id,
        'title' => single_term_title('', false)
      ]);
    
    }
    
    protected function addDate()
    {
            
      $y = get_the_time('Y');
      $m = get_the_time('m');
      $d = get_the_time('d');
      
      $yearLabel = get_the_time($this->args['year']);
      $yearLink = get_year_link($y);
      
      if (!is_year()) {
        
        $this->addCrumb([
          'type' => 'date',
          'year' => $y,
          'title' => $yearLabel,
          'url' => $yearLink
        ]);
        
        $monthLabel = get_the_time($this->args['month']);
        $monthLink = get_month_link($y, $m);
        
        if (!is_month()) {
          
          $this->addCrumb([
            'type' => 'date',
            'month' => $m,
            'year' => $y,
            'title' => $monthLabel,
            'url' => $monthLink
          ]);
          
          $this->addCrumb([
            'type' => 'date',
            'day' => $d,
            'month' => $m,
            'year' => $y,
            'title' => get_the_time($this->args['day'])
          ]);
          
        } else {
          
          $this->addCrumb([
            'type' => 'date',
            'month' => $m,
            'year' => $y,
            'title' => $monthLabel
          ]);
          
        }  
        
      } else {
        
        $this->addCrumb([
          'type' => 'date',
          'year' => $y,
          'title' => $yearLabel,
        ]);
        
      }
      
    }
    
    protected function addAuthor()
    {
      
      global $wp_query;
      $author = $wp_query->get_queried_object();

      $this->addCrumb([
        'type' => 'author',
        'user_id' => $author->data->ID,
        'title' => sprintf($this->args['author'], $author->data->display_name)
      ]);
    
    }
    
    protected function addPostType($postType)
    {
      
      global $post;
      
      $this->addArchive(get_post_type_object($postType));
            
      if (!empty($post->post_parent)) {
        
        $remove = [get_option('page_on_front')];
        
        $ancestors = array_reverse(array_diff(get_ancestors($post->ID, $postType, 'post_type'), $remove));
        
        foreach ($ancestors as $ancestor) {
          
          $this->addCrumb([
            'type' => 'post_type',
            'post_type' => $ancestor->post_type,
            'pid' => $ancestor,
            'title' => get_the_title($ancestor),
            'url' => get_the_permalink($ancestor)
          ]);
          
        }
        
      }   
      
      $this->addCrumb([
        'type' => 'post_type',
        'post_type' => $postType,
        'pid' => get_the_ID(),
        'title' => get_the_title()
      ]);
      
    }
    
    protected function addArchive($object)
    {
      
      $type = strtolower(str_replace('WP_', '', get_class($object)));
      
      if ($object->rewrite !== false) {
        
        $page = get_page_by_path($object->rewrite['slug']);
        
        $pid = apply_filters('jinx_breadcrumbs_archive', isset($page) ? $page->ID : null, $type, $object->name);
        $pid = apply_filters("jinx_breadcrumbs_archive_{$type}", $pid, $object->name);
        $pid = apply_filters("jinx_breadcrumbs_archive_{$type}_{$object->name}", $pid);
        
        if (isset($pid)) {
          
          $this->addCrumb([
            'type' => 'post_type',
            'post_type' => 'page',
            'pid' => $pid,
            'title' => get_the_title($pid),
            'url' => get_the_permalink($pid)
          ]);
          
        } elseif (!$object->_builtin) {
        
          $this->addCrumb([
            'type' => 'archive',
            'title' => $object->label,
            'url' => get_home_url(null, $object->rewrite['slug'])
          ]);
        
        }
        
      } 
      
    }
    
    public function crumbs()
    {
      
      $list = [];
            
      $crumbs = apply_filters('jinx_breadcrumbs_crumbs', $this->crumbs);
      
      $n = count($crumbs);

      foreach ($crumbs as $i => $crumb) {
        
        if (is_array($crumb)) {
          $crumb = new Crumb($crumb);
        }
        
        $item = sprintf($this->args['before_item'], $i === $n-1 ? ' aria-current="page"' : '');
          $item .= empty($crumb->url) ? $crumb->title : '<a href="'.$crumb->url.'">'.$crumb->title.'</a>';
        $item  .= $this->args['after_item'];
        
        $list[] = $item;
        
      }
            
      return $this->args['before'].implode('', $list).$this->args['after'];
    
    }
    
  }
