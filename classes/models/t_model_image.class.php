<?php

/**
 *
 * copied from model_image_rwd
 *
 * A class for table rows with corresponding (1) photo file on the filesystem
 *
 * <code>
 * CLASS user_photo EXTENDS model_image_rwd {
 *    const TABLE = 'user_photos';
 *    const TABLE_PK = 'photo_id';
 *
 *    public function file_path_name() {
 *        return add::config()->images_dir.'/'.$this->user_id.'-'.$this->id().'.jpg';
 *    }
 *
 *    public function img_src() {
 *        return add::config()->images_path.'/'.$this->user_id.'-'.$this->id().'.jpg';
 *    }
 * }
 * </code>
 *
 * @package ADD MVC Models\Extras
 * @since ADD MVC 0.0
 * @version 0.0
 */
TRAIT t_model_image
{
   public static $image_max_width = 2560;
   public static $image_max_height = 2560;
   public static $image_max_filesize = 26214400;

   /**
    * The imagesize cache
    *
    * @since ADD MVC 0.0
    */
   protected $image_size;

   /**
    * file_path_name
    * returns the file system path of the image
    * @return string $file_path_name
    * @author albertdiones@gmail.com
    */
   abstract public function file_path_name();

   /**
    * @param $orig_image
    * @param $max_width
    * @param $max_height
    * @return array
    * @throws e_unknown
    */
   public static function resize_ratios($orig_image, $max_width, $max_height)
   {
      if (!self::is_gd_resource($orig_image))
         throw new e_unknown("orig image is not gd resource " . gettype($orig_image));

      $orig_width = imagesx($orig_image);
      $orig_height = imagesy($orig_image);

      $orig_image_wider = $orig_width > $max_width;
      $orig_image_taller = $orig_height > $max_height;
      $orig_image_larger = $orig_image_wider || $orig_image_taller;
      if (!$orig_image_larger) {
         return array(1,1);
      }
      else {
         $width_resize_ratio = 1;
         $height_resize_ratio = 1;
         if ($orig_image_wider) {
            $width_resize_ratio = $max_width / $orig_width;
         }
         if ($orig_image_taller) {
            $height_resize_ratio = $max_height / $orig_height;
            return array($width_resize_ratio, $height_resize_ratio);
         }
         return array($width_resize_ratio, $height_resize_ratio);

      }
      return array($width_resize_ratio, $height_resize_ratio);
   }




   /**
    * img_src()
    * returns the url of the image
    * @return string $img_src
    * @author albertdiones@gmail.com
    */
   abstract public function img_src();

   /**
    * Gets the file extension
    */
   public function file_extension() {
      return pathinfo($this->file_path_name(), PATHINFO_EXTENSION);
   }

   /**
    * Gets the file extension
    */
   public function content_type() {
      $file_extension = $this->file_extension();
      switch ($file_extension) {
         case 'jpg':
         case 'jpeg':
            return "image/jpeg";
         break;
         case 'png':
            return "image/png";
         break;
         case 'gif':
            return "image/gif";
         break;
         default:
            throw new e_system("Unknown file type: " . $file_extension);
      }
   }
   /**
    * File size of the saved image
    */
   public function filesize() {
      return filesize($this->file_path_name());
   }

   /**
    * User_photo::get_gd($arg1)
    * vehicle_picture::get_gd('photo');
    * vehicle_picture::get_instance(123)->get_gd();
    * param $arg1 if string, gets the gd resource of $_FILES[$arg1]
    * param string $arg1 the input[type=file] name
    */
   public function get_gd(/* Polymorphic */)
   {
      if (!isset($this) || (isset($this) && !$this instanceof self)) {
         $args = func_get_args();
         if (is_string($args[0])) {
            if (isset($_FILES[$args[0]])) {
               return self::get_gd_by_input($args[0]);
            } else if (file_exists($args[0])) {
               return self::get_gd_by_filename($args[0]);
            } else {
               throw new e_system("model_image_rwd::get_gd() argument is invalid", $args);
            }
         }
         else if (is_array($args[0]) && isset($args[0]['name']) && isset($args[0]['tmp_name']) ) {
            return self::get_gd_by_file_array($args[0]);
         }
         else if (self::is_gd_resource($args[0])) {
            return $args[0];
         }
      } else {
         return self::get_gd_by_filename($this->file_path_name());
      }
   }


   /**
    * get_gd_by_filename
    * Returns the gd resource of $file_path_name
    *
    * @param string $file_path_name the file path name
    *
    * @see get_gd()
    * @return resource
    * @author albertdiones@gmail.com
    */
   private static function get_gd_by_filename($file_path_name)
   {
      $extension = pathinfo($file_path_name, PATHINFO_EXTENSION);
      switch (strtolower($extension)) {
         case 'jpeg':
         case "jpg":
            $image_create_func = "imagecreatefromjpeg";
            break;
         case "gif":
            $image_create_func = "imagecreatefromgif";
            break;
         case "png":
            $image_create_func = "imagecreatefrompng";
            break;
         default:
            throw new e_user("Unrecognized file extension: $extension");
            break;
      }
      $gd_image = $image_create_func($file_path_name);
      if (!$gd_image)
         throw new e_user('Invalid or corrupted image');
      return $gd_image;
   }
   /**
    * @param $uploaded_file
    * @return mixed
    * @throws Exception
    * @throws e_unknown
    */
   private static function get_gd_by_file_array($uploaded_file)
   {
      $extension = pathinfo($uploaded_file['name'], PATHINFO_EXTENSION);

      switch (strtolower($extension)) {
         case "jpeg":
         case "jpg":
            $image_create_func = "imagecreatefromjpeg";
            break;
         case "gif":
            $image_create_func = "imagecreatefromgif";
            break;
         case "png":
            $image_create_func = "imagecreatefrompng";
            break;
         default:
            throw new e_user("Unrecognized file extension: $extension");
            break;
      }

      $gd_image = $image_create_func($uploaded_file['tmp_name']);
      if (!$gd_image)
         throw new e_hack('Invalid or corrupted image');
      return $gd_image;
   }
   /**
    * get_gd_by_input_name
    * returns the gd resource of uploaded file from $_FILES[$input_name]
    *
    * @param $input_name the input name
    *
    * @return resource
    * @author albertdiones@gmail.com
    */
   private static function get_gd_by_input($input_name)
   {
      $uploaded_file = $_FILES[$input_name];
      return self::get_gd_by_file_array($uploaded_file);
   }

   /**
    * Checks the existence of the corresponding image
    *
    * @since ADD MVC 0.0
    */
   public function file_exists()
   {
      return file_exists($this->file_path_name()) && !is_dir($this->file_path_name());
   }

   /**
    * Deletes the file and the database row
    *
    * @since ADD MVC 0.0
    */
   public function delete()
   {
      return $this->delete_file() && parent::delete();
   }

   /**
    * Deletes the corresponding image file
    *
    * @since ADD MVC 0.0
    */
   protected function delete_file()
   {
      return !$this->file_exists() || unlink($this->file_path_name());
   }

   /**
    * Dimension functions
    */
   /**
    * checks if $this->image_size is set, if not, fetches the file and sets (cache) imagesize for later use
    * @param boolean $refresh ignore cache
    */
   protected function set_imagesize($refresh = false)
   {
      if (!isset($this->image_size) || $refresh) {
         $this->image_size = getimagesize($this->file_path_name());
      }
   }

   /**
    * returns the width in pixel of the image file
    */
   public function width()
   {
      $this->set_imagesize();
      return $this->image_size[0];
   }

   /**
    * returns the height in pixel of the image file
    */
   public function height()
   {
      $this->set_imagesize();
      return $this->image_size[1];
   }

   /**
    * returns the proportional html size attributes, optionally according to the limits
    * @param $max_width the max width of the image
    * @param $max_height the max height of the image
    */
   public function html_size_attr($max_width = NULL, $max_height = NULL)
   {
      if ($max_width === NULL && $max_height == NULL) {
         return $this->image_size[3];
      } else {
         $aspect_ratio = $this->aspect_ratio();
         $wider = $aspect_ratio > 1;
         $taller = $aspect_ratio < 1;
         if ($wider) {
            $resize_ratio = $max_width / $this->width();
         } else if ($taller) {
            if (!$max_height) {
               $max_height = $max_width / $aspect_ratio;
            }
            $resize_ratio = $max_height / $this->height();
         } else {
            $resize_ratio = $max_width / $this->width();
         }
         $width = $this->width() * $resize_ratio;
         $height = $this->height() * $resize_ratio;
         return " width='{$width}px' height='{$height}px' ";
      }
   }

   /**
    * returns css style set that fills the parent html element with this image
    */
   public function filled_css_size_style()
   {
      $aspect_ratio = $this->aspect_ratio();

      $wider = $aspect_ratio > 1;
      $taller = $aspect_ratio < 1;

      $styles = array();

      $width = 100;
      $height = 100;

      if ($wider) {
         $styles[] = 'position:relative';
         $width = 100 * $aspect_ratio;
         $styles[] = 'left:-' . floor(($width - 100) / 2) . '%';
      } elseif ($taller) {
         $styles[] = 'position:relative';
         $height = 100 * (1 / $aspect_ratio);
         $styles[] = 'top:-' . floor(($height - 100) / 2) . '%';
      }

      $styles[] = 'width:' . floor($width) . '%';
      $styles[] = 'height:' . floor($height) . '%';

      return implode(";", $styles);
   }


   /**
    * returns css style set that fills the parent element with this image
    *
    * @param int $max_width the target size max width
    * @param int $max_height the target size max height
    *
    * @todo investigate what's the difference of this from filled_css_size_style
    */
   public function filled_css_size_style_px($max_width, $max_height)
   {
      $aspect_ratio = $this->aspect_ratio();

      $wider = $aspect_ratio > 1;
      $taller = $aspect_ratio < 1;

      $styles = array();

      if ($wider) {
         $styles[] = 'position:relative';
         $resize_ratio = $max_height / $this->height();
      } elseif ($taller) {
         $styles[] = 'position:relative';
         $resize_ratio = $max_width / $this->width();
      } else {
         $resize_ratio = $max_width / $this->width();
      }

      $width = floor($this->width() * $resize_ratio);
      $height = floor($this->height() * $resize_ratio);

      $styles[] = 'width:' . $width . 'px';
      $styles[] = 'height:' . $height . 'px';
      if ($wider) {
         $styles[] = 'left:-' . floor((($aspect_ratio - 1) / 2) * $width) . 'px';
      } else {
         $styles[] = 'top:-' . floor(((1 - $aspect_ratio) / 2) * $height) . 'px';
      }

      return implode(";", $styles);
   }


   /**
    * returns a relative css style that will make the image fit the parent html element
    */
   public function fit_css_size_style()
   {
      $aspect_ratio = $this->aspect_ratio();

      $wider = $aspect_ratio > 1;
      $taller = $aspect_ratio < 1;

      $styles = array();

      $width = 100;
      $height = 100;

      if ($wider) {
         $styles[] = 'position:relative';
         $height = (100 / $aspect_ratio);
         $styles[] = 'margin-top:' . floor((100 - $height) / 2) . '%';
      } elseif ($taller) {
         $styles[] = 'position:relative';
         $width = (100 / (1 / $aspect_ratio));
         $styles[] = 'left:' . floor((100 - $width) / 2) . '%';
      }

      $styles[] = 'width:' . floor($width) . '%';
      $styles[] = 'height:' . floor($height) . '%';

      return implode(";", $styles);
   }

   /**
    * Returns the aspect ratio of the image file
    */
   public function aspect_ratio()
   {
      if ($this->height())
         return $this->width() / $this->height();
      else
         return 1;
   }

   /**
    * Reads the file and output ( using readfile() )
    */
   public function readfile() {
      if (headers_sent()) {
         throw new e_developer("Attempt to output image file after headers is already sent");
      }
      header("Content-type: ". $this->content_type());
      header("Content-length: ".$this->filesize());
      readfile($this->file_path_name());
      add::shutdown(false);
   }
   /**
    * @param $orig_image
    * @param $resize_ratio
    * @return resource
    * @internal param $orig_width
    * @internal param $orig_height
    */
   public static function ratio_resize($orig_image, $resize_ratio) {
      $orig_width = imagesx($orig_image);
      $orig_height = imagesy($orig_image);
      $image_width = $orig_width * $resize_ratio;
      $image_height = $orig_height * $resize_ratio;
      $image = imagecreatetruecolor($image_width, $image_height);
      imagealphablending($image, false);
      imagesavealpha($image, true);

      imagecopyresampled(
         $image, $orig_image,
         0, 0, 0, 0, # start coords
         $image_width, $image_height, # destination image width & height
         $orig_width, $orig_height # source image width & height
      );
      return $image;
   }

   /**
    * limit_dimension($orig_image,$max_width,$max_height)
    * returns a resource gd that is resized according to max_width and max_height
    * @param resource $orig_image the gd resource of the original image
    * @param int $max_width
    * @param int $max_height
    */
   public static function limit_dimension($orig_image, $max_width, $max_height)
   {

      return call_user_func_array('static::resize_minimized',func_get_args());
   }

   /**
    * resize_minimized($orig_image,$max_width,$max_height)
    * returns a resource gd that is resized according to max_width and max_height
    * @param resource $orig_image the gd resource of the original image
    * @param int $max_width
    * @param int $max_height
    */
   public static function resize_minimized($orig_image, $max_width, $max_height)
   {

      list($width_resize_ratio, $height_resize_ratio) = static::resize_ratios($orig_image, $max_width, $max_height);

      $resize_ratio = min($width_resize_ratio, $height_resize_ratio);

      if ($resize_ratio == 1) {
         $image = $orig_image;
      }
      else {
         $image = static::ratio_resize($orig_image, $resize_ratio);
      }
      return $image;
   }
   /**
    * resize_maximized($orig_image,$min_width,$min_height)
    * returns a resource gd that is resized according to max_width and max_height
    * @param resource $orig_image the gd resource of the original image
    * @param int $min_width
    * @param int $min_height
    */
   public static function resize_maximized($orig_image, $min_width, $min_height)
   {

      list($width_resize_ratio, $height_resize_ratio) = static::resize_ratios($orig_image, $min_width, $min_height);

      $resize_ratio = max($width_resize_ratio, $height_resize_ratio);

      if ($resize_ratio == 1) {
         $image = $orig_image;
      }
      else {
         $image = static::ratio_resize($orig_image, $resize_ratio);
      }
      return $image;
   }

   /**
    * add_new
    * @param $data
    * #param string $image_arg the input[type=file][name]
    * OR
    * #param resource $image_arg the image resource
    *
    * @deprecated use add_new_image()
    */
   public static function add_new($data/*,$image_arg*/) {
      if (func_num_args() >= 2) {
         return static::add_new_image($data, func_get_arg(1));
      }
      else {
         return parent::add_new($data);
      }
   }

   /**
    * add_new
    * @param $data
    * @param string $image_arg the input[type=file][name]
    * OR
    * @param resource $image_arg the image resource
    */
   public static function add_new_image($data, $image_arg)
   {
      if (!$image_arg) {
         throw new e_system("Image parameter is empty");
      }

      static::db()->StartTrans();

      $image = parent::add_new($data);

      if ($image) {
         $image_gd = self::get_gd($image_arg);
         if (!$image->save_gd($image_gd)) {
            static::db()->FailTrans();
            throw new e_system("Failed tosave image to filesystem");
         }
      } else {
         throw new e_developer("Failed to insert image " . print_r($data, true));
      }

      static::db()->CompleteTrans();

      return $image;
   }


   /**
    * public save_gd($orig_image)
    *
    * @param $orig_image the gd resource of the image
    *
    */
   public function save_gd($orig_image)
   {
      if (!self::is_gd_resource($orig_image)) {
         throw new e_developer("\$orig_image is not gd (" . (is_resource($orig_image) ? "Resource type: " . get_resource_type($orig_image) : gettype($orig_image)) . ")");
      }

      $image_gd = self::resize_minimized($orig_image, static::$image_max_width, static::$image_max_height);


      $path_name = $this->file_path_name();
      $dir = dirname($path_name);

      if (!file_exists($dir)) {
         if (!mkdir($dir, 0777, true)) {
            throw new e_system("Failed to make image directory: $dir");
         }
      }
      elseif (!is_dir($dir)) {
            throw new e_developer("$dir is a file instead of a directory");
      }

      return self::save_gd2file($path_name, $image_gd);
   }
   /**
    * @param $path_name
    * @param $image_gd
    * @return bool
    * @throws e_developer
    */
   public static function save_gd2file($path_name, $image_gd)
   {
      switch (pathinfo($path_name, PATHINFO_EXTENSION)) {
         case 'png':
            imagealphablending($image_gd, false);
            imagesavealpha($image_gd, true);
            $image_created = imagepng($image_gd, $path_name);
            break;
         case 'gif':
            imagealphablending($image_gd, false);
            imagesavealpha($image_gd, true);
            $image_created = imagegif($image_gd, $path_name);
            break;
         case 'jpg':
         default:
            $image_created = imagejpeg($image_gd, $path_name);
      }

      if (!$image_created) {
         throw new e_developer("Failed to save image on path: " . $path_name, $image_gd);
      } else {
         return true;
      }
   }

   /**
    * Checks if $arg is a gd resource
    *
    * @param mixed $arg the variable to check
    *
    * @since ADD MVC 0.0
    */
   static function is_gd_resource($arg)
   {
      return is_resource($arg) && get_resource_type($arg) === 'gd';
   }

   /**
    * returns if filesize is ok
    * @param string $image_arg currently only the input[name] of the [type=file] input
    */
   static function check_file_size($image_arg)
   {
      if (is_string($image_arg)) {
         return $_FILES[$image_arg]['size'] <= static::$image_max_filesize;
      }
      return true;
   }
}