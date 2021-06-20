<?php

namespace App\Http\Controllers;


use App\Models\Comments;
use App\Models\Follows;
use App\Models\Likes;
use App\Models\Messages;
use App\Models\Pictures;
use App\Models\Posts;
use App\Models\Story;
use App\Models\User;
use Illuminate\Http\Request;

class DataRelationTest extends Controller
{
    public function index(){
        $O = [];

        $user1 = User::find(1);
        $user2 = User::find(2);
        $user3 = User::find(3);
        $user4 = User::find(4);

        $post1 = Posts::find(1);
        $post2 = Posts::find(2);
        $post3 = Posts::find(3);
        $post4 = Posts::find(4);


        $message1 = Messages::find(1);

        $story1 = Story::find(1);

        $picture1 = Pictures::find(1);

        $comment1 = Comments::find(1);

        $Data = [
            'users'=>[
                $user1,$user2,$user3,$user4
            ],
            'posts'=>[
                $post1,$post2,$post3,$post4
            ],
        ];
        $UserPostOneToMany = [
            'user post one to many'=>[
                'user1 post '=>[
                    $user1->posts
                ],
                'post1 user '=>[
                    $post1->user
                ]
            ],
        ];
        $UserFollow = [
            'user follow'=>[
                'all'=>[
                    Follows::all()
                ],

                'user1 following '=>[
                    $user1->following
                ],
                'user2 follower '=>[
                    $user1->followers
                ]

            ],
        ];
        $UserMessageOneToMany = [
            'user message one to many'=>[
                'message'=>[
                    Messages::all()
                ],
                'user1 message sent'=>[
                    $user1->sent_messages
                ],
                'user2 message received'=>[
                    $user2->received_messages
                ],
                'message1 from user'=>[
                    $message1->from
                ],
                'message1 to user'=>[
                    $message1->to
                ],
            ],
        ];
        $UserStoryOneToMany = [
            'user story one to many'=>[
                'story'=>[
                    Story::all()
                ],
                'user1 story'=>[
                    $user1->story
                ],
                'story1 user'=>[
                    $story1->user
                ]
            ],
        ];
        $PostPicturesOneToMany = [
            'post pictures one to many'=>[
                'pictures'=>[
                    Pictures::all()
                ],
                'post1 pictures'=>[
                    $post1->pics
                ],
                'picture1 post'=>[
                    $picture1->post
                ]
            ],
        ];
        $UserLikesOneToMany = [
            'user like one to many'=>[
                'likes'=>[
                    Likes::all()
                ],
                'user3 likes'=>[
                    $user3->likes
                ],
                'user4 likes post'=>[
                    $user4->user_likes_posts
                ]

            ],
        ];
        $PostLikesOneToMany = [
            'post like one to many'=>[
                'post2 likes'=>[
                    $post2->likes
                ],
                'post2 likes users'=>[
                    $post2->post_likes_users
                ]
            ],
        ];
        $UserCommentsOneToMany = [
            'user comments one to many'=>[
                'comments'=>[
                    Comments::all()
                ],
                'user1 comments'=>[
                    $user1->comments
                ],
                'comment1 user'=>[
                    $comment1->user
                ],
                'user comments post'=>[
                    $user1->user_comments_posts
                ],
            ],
        ];
        $PostCommentsOneToMany = [
            'post comments one to many'=>[
                'post1 comments'=>[
                    $post1->comments
                ],
                'comment1 post'=>[
                    $comment1->post
                ],
                'post comments users'=>[
                    $post1->post_comments_users
                ]
            ],
        ];
        $O[] = [
            $UserPostOneToMany,$UserFollow
        ];

        return '<pre>'.json_encode($O,JSON_PRETTY_PRINT);
    }
}
