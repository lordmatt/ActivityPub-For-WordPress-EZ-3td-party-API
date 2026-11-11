<?php
namespace ActivityPub\EZ3P; 
/**
 * The purpose of this project is to create an all in one easy API for things to
 * do with the WordPress ActivtyPub plugin.
 * 
 * My intention is to make a simple library and working tutorial for some simple 
 * uses of the ActivityPub plugin.
 * 
 * I would love to include any snippets that you are prepared to share under the
 * MIT license for working with ActivityPub.
 * 
 * This all started as a feature request: <https://github.com/Automattic/wordpress-activitypub/issues/4058>
 *
 * @author lordmatt
 * @status draft - incomplete !!
 * 
 * This project used code written for a bunch of other projects by Matthew Brown
 * 
 * MIT License
 * 
 * Copyright (c) 2026 Matthias Pfefferle
 * Copyright (c) 2026 Matthew Brown except as noted:
 * Copyright (c) 2026 Matthew Brown on OpenMentions <https://openmentions.com>
 * Copyright (c) 2025 Matthew Brown on isBrill <https://isbrill.com>
 * Copyright (c) 2026 Matthew Brown on Matt's Social Node <https://node.lordmatt.co.uk>
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
class common_ap_alias {
   

    
    static public function get_extra_fields($userid){
        // should just be a get CPT $type for $userid
    }
    
    static public function purge_reader_unused_tags(){
        // still to figure out how
    }
    
    static public function purge_reader($userid){
        // still to figure out how
    }
    
    static public function get_biography($userid){
        // likely to be a somewhat internal Wordpress call
    }
    static public function set_biography($userid){
        // likely to be a somewhat internal Wordpress call
    }
    
    // followers
    // Initial code for followers used on <https://isbrill.com>
    
    /**
     * Get an array of followers for the blog
     * 
     * @param int $blogid
     * @return array
     */
    public static function get_blog_followers($blogid=1){
        $followers = Activitypub\Collection\Followers::get_many( $blogid );
        return $followers; 
    }
    
    /**
     * Get an array of followers for the blog
     * 
     * @param int $userid
     * @return array
     */
    public static function get_user_followers($user_id){
        return Activitypub\get_followers( $user_id );
    }
    
    public static function get_follower_object($id){
        return \Activitypub\Collection\Remote_Actors::get_actor($id);
    }
    
    /**
     * Example of using the fedi follower object
     * @param int $id
     * @return int - image URL
     */
    public static function get_fedi_follower_icon($id){
        $fedi_follower_object = self::get_fedi_follower_icon($id);
        return $fedi_follower_object->get_icon();
    }
    
    /**
     * Get the number of followers for blog or user
     * 
     * $userid=0 indicates to do this for the  current blog
     * 
     * This version thanks to comment by the lovely Matthias Pfefferle  
     * 
     * @param int $userid
     * @return int
     */
    public static function get_follower_count($userid=0){
        return Activitypub\count_followers( $userid );
    }   
    
    /**
     * To call for another blog, change the working blog call this and then
     * change back to the current blog
     * 
     * This version thanks to comment by the lovely Matthias Pfefferle 
     * 
     * Was an alias of get_follower_count(0);
     * @return int
     */
    public static function get_follower_count_blog() {
        return Activitypub\count_followers( 0 );
    }
    
    public static function get_follower_count_for_other_blog($blogid){
        switch_to_blog( $blog_id );
        $count = self::get_follower_count_blog();
        restore_current_blog();
        return $count;
    }
    
    // following
    public static function get_following($userid=0){
        // I've yet to figure this out
    }
    
    /**
     * Pass a remote actor and a user (or zero for current blog) to follow said
     * remote actor.  $remote_actor can be a URL, a WebFinger handle or a post ID.
     * 
     * This version thanks to comment by the lovely Matthias Pfefferle 
     * @param string $remote_actor - see notes
     * @param int $user_id
     * @return unknown
     */
    public static function follow($remote_actor, $user_id=0){
        return Activitypub\follow( $remote_actor, $user_id );
    }
    
    public static function unfollow($remote_actor, $user_id=0){
        return Activitypub\unfollow( $remote_actor, $user_id );
    }
    
    // stats and data
    
    public static function get_stats($user_id=0){
        // I haven't a clue right now. I hope this is easy to do
    }
    
    public static function reactions_for_post($postID) {
        // @todo figure this out
    }
    
    /**
     * Get the number of reactions on post for a given time range
     * @param int $start unix timestamp
     * @param int $end unix timestamp
     * @return int
     */
    public static function reactions_by_time(int $start, int $end){
        $count =0;
        // @todo write code
        return $count;
    }
    
    // templates and theme tweaks
    
    /**
     * A template function to make some text for all types of reactions which it
     * turns out did not touch ActivityPub at all.
     * 
     * First used on <https://node.lordmatt.co.uk>
     */
    public static function template_ractions_for_post(){
        $comments = get_comments(array(
            'post_id' => get_the_ID(),
            'status'  => 'approve',
            'type'    => 'all',
        ));
        $reactions = count($comments);
        echo "| ";
        switch($reactions){
            case 0:
                echo "Tumbleweed";
            break;
            case 1:
                echo "One Lonely Reaction";
            break;
            default:
                echo "{$reactions} reactions";

        }
    }
    
    // fixes, tweaks, and edge cases
    
    /**
     * For some configurations, WebMentions and ActivityPub Replies might not be
     * counted properly. This function adjusts for that.
     * 
     * Add as filter add_filter('get_comments_number', [self, 'comment_count_corrected'], 10, 2);
     * 
     * First used on <https://node.lordmatt.co.uk>
     * 
     * @param int $count
     * @param int $post_id
     * @return int
     */
    public function comment_count_corrected($count, $post_id) {

        $comments = get_comments([
            'post_id' => $post_id,
            'status'  => 'approve',
            'type'    => 'all',
        ]);

        $comments = array_filter($comments, function($c) {
            $type = strtolower(get_comment_type($c));

            return !in_array($type, ['like', 'repost', 'boost',], true);
        });

        return count($comments);

    }
    
    /**
     * 
     * If WebMention is also installed, will force the output of Facepile in
     * the theme.
     * 
     * Ideally, it would be good to write an EZ3P call for when WebMention is 
     * not installed
     * 
     * Initial code from <https://openmentions.com>
     */
    static public function template_output_facepile(){
        if ( is_callable( [ '\Webmention\Comment_Walker', 'show_separated_reactions' ] ) ) {
            \Webmention\Comment_Walker::show_separated_reactions();
        }
    }
    
}
