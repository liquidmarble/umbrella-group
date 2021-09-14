<?php

class umbrella_feedback
{
    public $err = "error";

    public function fill_attributes(): bool
    {
        return true;
    }

    public function generate_shortcode()
    {
        $clients = $this->get_clients();
        $html = <<<EOHTML
            [section bg_color="rgb(249, 249, 249)"]
                [row]
                    [col span__sm="12" padding="20px 0px 0px 0px"]
                        <h2>Клиенты о нас</h2>
                        <p class="feedback-subheader">Несколько историй от людей, которые доверили нам свой бизнес.</p>
                        <p class="feedback-subheader-right"><a style="text-decoration: underline;" href="/about/feedback/">Все отзывы</a></p>
                        [gap]
                        [ux_slider bg_color="rgb(255, 255, 255)" draggable="false" hide_nav="true" nav_style="simple" bullet_style="square"]
                            $clients
                        [/ux_slider]
                    [/col]
                [/row]
            [/section]
        EOHTML;
        return do_shortcode($html);
    }

    private function get_clients()
    {
        $tag = get_term_by('slug', 'show-on-main', 'client_tags');
        if (!empty($tag)) {
            $args = array(
                'numberposts' => 99,
                'post_type' => 'client',
                'tax_query' => array(
                    array(
                        'taxonomy' => $tag->taxonomy,
                        'field' => $tag->taxonomy,
                        'terms' => $tag->term_id,
                    ),
                )

            );
            $posts = get_posts($args);
        }
        if (!empty($posts)) {
            $html="";
            foreach ($posts as $post) {
                $logo = get_the_post_thumbnail($post->ID);
                $logo = '<div class="feedback-image">' . $logo . '</div>';
                $industry = get_post_meta($post->ID, 'umbrella_client_personal_industry', true);
                $industry = '<div class="feedback-industry">' . $industry . '</div>';
                $title = $post->post_title;
                $titleAndIndustry = '<div class="feedback-item-header"> ' . $title . $industry . '</div>';
                $excerpt = get_the_excerpt($post->ID);
                $excerpt = '<div class="feedback-excerpt">' . $excerpt . '</div>';
                $scan = get_post_meta($post->ID, 'umbrella_feedback_scan', true);
                $scan = '[ux_image id="' . $scan . '" width="100" height="328px" lightbox="true" depth_hover="3" depth="1" ]';
                $html .= '[row_inner padding="40px 0 40px 0" style="large" v_align="top" class="feedback-row"]';
                if (!empty($scan)) {
                    $html .= <<<EOHTML
                        [col_inner span="3" margin="0px 0px 0px 0px" class="hide-for-medium"] 
                         $scan 
                        [/col_inner] 
                        [col_inner span="9" span__sm="12" align="left" margin="0px 0px -30px 0px"] 
                            $titleAndIndustry 
                            $logo 
                            $excerpt 
                        [/col_inner]
                    EOHTML;
                } else {
                    $html .= <<<EOHTML
                        [col_inner span="12" span__sm="12" align="left" margin="0px 0px -30px 0px"]
                            $titleAndIndustry 
                            $logo 
                            $excerpt 
                        [/col_inner]
                    EOHTML;
                }
                $html .= "[/row_inner]";

            }
        }
        return $html;
    }
}

function umbrella_feedback_block_shortcode($atts)
{
    $shortcode = new umbrella_feedback();
    $shortcode->atts = $atts;
    if (!$shortcode->fill_attributes()) {
        return $shortcode->err;
    }
    return $shortcode->generate_shortcode();
}

add_shortcode('umbrella_feedback', 'umbrella_feedback_block_shortcode');