<?php

namespace App\Http\Controllers;

use Facebook\HttpClients\FacebookGuzzleHttpClient;
use Guzzle\Http\Client;
use Illuminate\Http\Request;

use App\Http\Requests;


class InstagramController extends Controller
{
    public $instagram;

    public function __construct()
    {
        $this->instagram = new \InstagramAPI\Instagram();
        $username = Data::get('inUser');
        $password = Data::get('inPass');

        try {
            $this->instagram->setUser($username, $password);
            $this->instagram->login();
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }


    }

    /**
     * My feed
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $i = $this->instagram;
        $datas = $i->getSelfUserFeed();
        return view('instagram', compact('datas'));
    }


    /**
     * Popular feed according to user likes and views
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function popular()
    {

        $i = $this->instagram;
        $datas = $i->getPopularFeed();

        return view('instagramPopular', compact('datas'));
    }

    /**
     * Get followers count
     * @return string
     */
    public function getFollowers()
    {
        try {
            return $this->instagram->getSelfUsernameInfo()->user->follower_count;
        } catch (\Exception $exception) {
            return "Error";
        }

    }

    /**
     * Get following count
     * @return string
     */
    public function getFollowing()
    {
        try {
            return $this->instagram->getSelfUsernameInfo()->user->following_count;
        } catch (\Exception $exception) {
            return "Error";
        }

    }

    /**
     * Get the users activity whome we follow
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getFollowingUserActivity()
    {
        $datas = $this->instagram->getFollowingRecentActivity();
//        var_dump($datas);
//        exit;
        return view('instagramFollowingActivity', compact('datas'));
    }

    /**
     * Home page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function home()
    {
        $datas = $this->instagram->timelineFeed();

        return view('instagramTimeline', compact('datas'));
    }


    public function test()
    {
        $i = $this->instagram;

        $datas = $i->timelineFeed();
        print_r($datas);
    }

    /**
     * Write new post to instagram
     * @param Request $request
     * @return string
     */
    public function write(Request $request){
        try{
            $this->instagram->uploadPhoto(public_path()."/uploads/".$request->image,$request->caption);
            return "success";
        }catch (\Exception $exception){
            return $exception->getMessage();
        }

    }

    public function delete(){
        print_r($this->instagram->deleteMedia('1412975252606226869_4310486200'));
    }

    public function getMediaInfoIndex(){
        return view('instagramMediaInfo');
    }

    public function getMediaInfo($mediaId){
        $datas = $this->instagram->mediaInfo($mediaId);
//        print_r($datas);
//        exit;
        $data = $datas->items[0];
        return view('instagramMediaInfo',compact('data'));
    }

    public function followers(){
        $datas = $this->instagram->getSelfUserFollowers();

        return view('instagramFollowers',compact('datas'));
    }

    public function following(){
        $datas = $this->instagram->getSelfUsersFollowing();

        return view('instagramFollowing',compact('datas'));
    }

    public function autoFollowIndex(){
        return view('instagramAutoFollow');
    }

}
