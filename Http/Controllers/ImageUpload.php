<?php
/**
 * Created by mr.vjcspy@gmail.com/khoild@smartosc.com.
 * Date: 2/16/16
 * Time: 12:14 AM
 */

namespace Modules\IzCore\Http\Controllers;

use Illuminate\Http\Request;
use Response;

trait ImageUpload {

    protected $_imageFolder;
    /**
     * @var \Intervention\Image\ImageManager
     */
    protected $Image;


    public function postUpload(Request $request) {
        $this->Image = app()['image'];
        
        //get file:
        $file       = $request->file('file');
        $image_name = $file->getClientOriginalName();

        if (is_null($this->_imageFolder)) {
            $file->move('uploads', $image_name);
            $this->Image->make(sprintf('uploads/%s', $image_name))->save();
        }
        else {
            //move file to folder
            $file->move('uploads/' . $this->_imageFolder, $image_name);
            // save image by Image
            $this->Image->make(sprintf('uploads/' . $this->_imageFolder . '/%s', $image_name))->save();
        }

        return Response::json(
            [
                'success'          => true,
                'files'            => $_FILES,
                'get'              => $_GET,
                'post'             => $_POST,
                //optional
                'flowTotalSize'    => isset($_FILES['file']) ? $_FILES['file']['size'] : $_GET['flowTotalSize'],
                'flowIdentifier'   => isset($_FILES['file']) ? $_FILES['file']['name'] . '-' . $_FILES['file']['size']
                    : $_GET['flowIdentifier'],
                'flowFilename'     => isset($_FILES['file']) ? $_FILES['file']['name'] : $_GET['flowFilename'],
                'flowRelativePath' => isset($_FILES['file']) ? $_FILES['file']['tmp_name'] : $_GET['flowRelativePath']
            ],
            200);
    }

    public function getUpload() {
        return Response::json([], 204);
    }
}
