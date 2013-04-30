<?php
/* Template name: How do I? page */

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); 
?>

<div class="row white">
	<div class="twelvecol last">
		<div class="row">
			<div class='breadcrumbs'>
				<?php if(function_exists('bcn_display') && !is_front_page()) {
				bcn_display();
				}?>
			</div>
		</div>
		<div class="content-wrapper">
		
			<h1><?php echo the_title(); ?></h1>
			<?php echo the_content(); ?>
		</div>					
	</div>
</div>

<!-- category search box -->
<div class="row white">
	<div class="category-search">
		<div id="sbc">
		<form method="get" id="sbc-search" action="/">
			<input type="hidden" name="post_type" value="task" />
			<select name='cat' id='cat' class='postform'>
				<option value='0' selected='selected'>All tasks and guides</option>
				<?php
				$terms = get_terms('category');
					if ($terms) {
				  		foreach ((array)$terms as $taxonomy ) {
				  			if ($taxonomy->name == 'Uncategorized'){
					  			continue;
				  			}
					  		echo "<option class='level-0' value='".$taxonomy->term_id."'>".$taxonomy->name."</option>";
					  		}
					  		}
				?>
				</select>

			<input type="text" value="" name="s" id="sbc-s" class="multi-cat" onblur="if (this.value == '') {this.value = '';}"  onfocus="if (this.value == '') {this.value = '';}" />
			<input type="submit" class="small awesome blue" id="sbc-submitx" value="Search" />
		</form>
	</div>
	

	</div>
</div>


<?php
// Display category blocks

$catcount = 0;
$terms = get_terms('category');
	if ($terms) {
  		foreach ((array)$terms as $taxonomy ) {
  		    $themeid = $taxonomy->term_id;
  		    $themeURL= $taxonomy->slug;
   		    if ($themeURL == 'uncategorized') {
	  		    continue;
  		    }
  		    $catcount++;
  		    if ($catcount==4)
  		    {
	  		    $catcount=1;
  		    }
  		    if ($catcount==1){
				echo "<div class='row white'><br>";
				echo "<div class='content-wrapper'>";
  		    }
  			echo "
			<div class='fourcol white";
			if ($catcount==3){
			echo ' last';
			} 
			echo "'>
				<div class='category-block brd" . $taxonomy->term_id ."'>
					<h2><a href='/task-by-category/?cat={$themeURL}'>".$taxonomy->name."</a></h2>
					<p>".$taxonomy->description."</p>
				</div>
			</div>";
			if ($catcount==3){
				echo '</div></div>';
			}
		}
			if ($catcount==3){
				echo "<div class='row white'><br><div class='content-wrapper'>";
			}
			if ($catcount==2){
				echo "</div><div class='row white'><br><div class='twelvecol last'>";
			}
			if ($catcount==1){
				echo "</div></div><div class='row white'><br><div class='content-wrapper'>";
			}						
	}  

// Big tag cloud
?>

<div class="bbp-template-notice info">

	<h3>Search by tag</h3>
	<?php echo my_colorful_tag_cloud('','category','task'); ?>
	<br><br>
</div><br>
</div>
<?php 

if ($catcount == 3){
echo "</div>";
			}
if ($catcount == 2){
echo "</div></div>";
			}
if ($catcount == 1){
echo "</div>";
			}
?>			

<?php endwhile; ?>

<?php get_footer(); ?>