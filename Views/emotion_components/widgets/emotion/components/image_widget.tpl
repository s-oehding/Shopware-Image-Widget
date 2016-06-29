{foreach $Data.files as $file}
	<div class="banner-wrapper">
	    <div class="teaser-banner">
	        <div class="emotion--category-teaser">
				{if $Data.image_link}
	            	<a href="{$Data.image_link}"><img src="{$file.path}" {if $Data.image_alt_tag} alt="{$Data.image_alt_tag}" {/if} {if $Data.image_title} title="{$Data.image_title}" {/if} ></a>
				{else}
					<img src="{$file.path}"{if $Data.image_alt_tag} alt="{$Data.image_alt_tag}" {/if} {if $Data.image_title} title="{$Data.image_title}" {/if} >
				{/if}
				{if $Data.teaser_text}
	            <span class="category-teaser--title">
	                {$Data.teaser_text}
	            </span>
	            {/if}
	        </div>
	    </div>
    </div>
{/foreach}