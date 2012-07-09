<?php
/**
 * model_image_rwd
 * A class for db entities that are images
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC\Models\Extras
 * @since ADD MVC 0.0
 * @version 0.0
 */
ABSTRACT CLASS model_image_rwd EXTENDS model_rwd {
  const PIC_MAX_WIDTH =600;
  const PIC_MAX_HEIGHT=600;
  const PIC_MAX_FILE_SIZE = 278528;

  /**
   * The imagesize cache
   *
   * @since ADD MVC 0.0
   */
  protected $imagesize;

   /**
    * file_path_name
    * returns the file system path of the image
    * @return string $file_path_name
    * @author albertdiones@gmail.com
    */
   abstract public function file_path_name();

   /**
    * img_src()
    * returns the url of the image
    * @return string $img_src
    * @author albertdiones@gmail.com
    */
   abstract public function img_src();

   /**
   * User_photo::get_gd($arg1)
   * vehicle_picture::get_gd('photo');
   * vehicle_picture::get_instance(123)->get_gd();
   * @param $arg1 if string, gets the gd resource of $_FILES[$arg1]
   * @param string $arg1 the input[type=file] name
   */
   public function get_gd(/* Polymorphic */) {
      if (!isset($this) || (isset($this) && !$this instanceof self)) {
         $args = func_get_args();
         if (is_string($args[0]) && isset($_FILES[$args[0]])) {
            return self::get_gd_by_input($args[0]);
         }
         else if (self::is_gd_resource($args[0])) {
            return $args[0];
         }
      }
      else {
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
   private static function get_gd_by_filename($file_path_name) {
      $extension = pathinfo($file_path_name,PATHINFO_EXTENSION);
      switch (strtolower($extension)) {
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
    * get_gd_by_input_name
    * returns the gd resource of uploaded file from $_FILES[$input_name]
    *
    * @param $input_name the input name
    *
    * @return resource
    * @author albertdiones@gmail.com
    */
   private static function get_gd_by_input($input_name) {
      $uploaded_file = $_FILES[$input_name];
      $extension = pathinfo($uploaded_file['name'],PATHINFO_EXTENSION);

      switch (strtolower($extension)) {
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
            throw new Exception("Unrecognized file extension: $extension");
         break;
      }

      $gd_image = $image_create_func($uploaded_file['tmp_name']);
      if (!$gd_image)
         throw new Exception('Invalid or corrupted image');
      return $gd_image;
   }

   /**
    * Checks the existence of the corresponding image
    *
    * @since ADD MVC 0.0
    */
   public function file_exists() {
      return file_exists($this->file_path_name()) && !is_dir($this->file_path_name());
   }

   /**
    * Deletes the file and the database row
    *
    * @since ADD MVC 0.0
    */
   public function delete() {
      return $this->delete_file() && parent::delete();
   }

   /**
    * Deletes the corresponding image file
    *
    * @since ADD MVC 0.0
    */
   protected function delete_file() {
      return !$this->file_exists() || unlink($this->file_path_name());
   }

/**
 * Dimension functions
 */
   /**
    * checks if $this->imagesize is set, if not, fetches the file and sets (cache) imagesize for later use
    * @param boolean $refresh ignore cache
    */
   protected function set_imagesize($refresh=false) {
      if (!isset($this->imagesize) || $refresh) {
         $this->imagesize = getimagesize($this->file_path_name());
      }
   }

   /**
    * returns the width in pixel of the image file
    */
   public function width() {
      $this->set_imagesize();
      return $this->imagesize[0];
   }

   /**
    * returns the height in pixel of the image file
    */
   public function height() {
      $this->set_imagesize();
      return $this->imagesize[1];
   }

   /**
    * returns the proportional html size attributes, optionally according to the limits
    * @param $max_width the max width of the image
    * @param $max_height the max height of the image
    */
   public function html_size_attr($max_width=NULL,$max_height=NULL) {
      if ($max_width===NULL && $max_height==NULL) {
         return $this->imagesize[3];
      }
      else {
         $aspect_ratio = $this->aspect_ratio();
         $wider = $aspect_ratio > 1;
         $taller = $aspect_ratio < 1;
         if ($wider) {
            $resize_ratio = $max_width/$this->width();
         }
         else if ($taller){
            if (!$max_height) {
               $max_height = $max_width/$aspect_ratio;
            }
            $resize_ratio = $max_height/$this->height();
         }
         else {
            $resize_ratio = $max_width/$this->width();
         }
         $width = $this->width()*$resize_ratio;
         $height = $this->height()*$resize_ratio;
         return " width='{$width}px' height='{$height}px' ";
      }
   }

   /**
    * returns css style set that fills the parent html element with this image
    */
   public function filled_css_size_style() {
      $aspect_ratio = $this->aspect_ratio();

      $wider = $aspect_ratio > 1;
      $taller = $aspect_ratio < 1;

      $styles = array();

      $width = 100;
      $height = 100;

      if ($wider) {
         $styles[] = 'position:relative';
         $width = 100*$aspect_ratio;
         $styles[] = 'left:-'.floor(($width-100)/2).'%';
      }

      elseif ($taller) {
         $styles[] = 'position:relative';
         $height = 100*(1/$aspect_ratio);
         $styles[] = 'top:-'.floor(($height-100)/2).'%';
      }

      $styles[] = 'width:'.floor($width).'%';
      $styles[] = 'height:'.floor($height).'%';

      return implode(";",$styles);
   }


   /**
    * returns css style set that fills the parent element with this image
    *
    * @param int $max_width the target size max width
    * @param int $max_height the target size max height
    *
    * @todo investigate what's the difference of this from filled_css_size_style
    */
   public function filled_css_size_style_px($max_width,$max_height) {
      $aspect_ratio = $this->aspect_ratio();

      $wider = $aspect_ratio > 1;
      $taller = $aspect_ratio < 1;

      $styles = array();

      if ($wider) {
         $styles[] = 'position:relative';
         $resize_ratio = $max_height/$this->height();
      }

      elseif ($taller) {
         $styles[] = 'position:relative';
         $resize_ratio = $max_width/$this->width();
      }

      else {
         $resize_ratio = $max_width/$this->width();
      }

      $width = floor($this->width()*$resize_ratio);
      $height = floor($this->height()*$resize_ratio);

      $styles[] = 'width:'.$width.'px';
      $styles[] = 'height:'.$height.'px';
      if ($wider) {
         $styles[] = 'left:-'.floor((($aspect_ratio-1)/2)*$width).'px';
      }
      else {
         $styles[] = 'top:-'.floor(((1-$aspect_ratio)/2)*$height).'px';
      }

      return implode(";",$styles);
   }


   /**
    * returns a relative css style that will make the image fit the parent html element
    */
   public function fit_css_size_style() {
      $aspect_ratio = $this->aspect_ratio();

      $wider = $aspect_ratio > 1;
      $taller = $aspect_ratio < 1;

      $styles = array();

      $width = 100;
      $height = 100;

      if ($wider) {
         $styles[] = 'position:relative';
         $height = (100/$aspect_ratio);
         $styles[] = 'margin-top:'.floor((100-$height)/2).'%';
      }

      elseif ($taller) {
         $styles[] = 'position:relative';
         $width = (100/(1/$aspect_ratio));
         $styles[] = 'left:'.floor((100-$width)/2).'%';
      }

      $styles[] = 'width:'.floor($width).'%';
      $styles[] = 'height:'.floor($height).'%';

      return implode(";",$styles);
   }

   /**
    * Returns the aspect ratio of the image file
    */
   public function aspect_ratio() {
      if ($this->height())
         return $this->width()/$this->height();
      else
         return 1;
   }
   /**
    * limit_dimension($orig_image,$max_width,$max_height)
    * returns a resource gd that is resized according to max_width and max_height
    * @param resource $orig_image the gd resource of the original image
    * @param int $max_width
    * @param int $max_height
    */
   public static function limit_dimension($orig_image,$max_width,$max_height) {

      if (!self::is_gd_resource($orig_image))
         throw new e_unknown("orig image is not gd resource ".gettype($orig_image));

      $orig_width    = imagesx($orig_image);
      $orig_height   = imagesy($orig_image);

      $orig_image_wider = $orig_width > $max_width;
      $orig_image_taller = $orig_height > $max_height;
      $orig_image_larger = $orig_image_wider || $orig_image_taller;
      if ($orig_image_larger) {
         if ($orig_image_wider) {
            $resize_ratio = $width_resize_ratio = $max_width/$orig_width;
         }
         if ($orig_image_taller) {
            $resize_ratio = $height_resize_ratio = $max_height/$orig_height;
         }
         if ($orig_image_wider && $orig_image_taller) {
            $resize_ratio = min($width_resize_ratio,$height_resize_ratio);
         }

         $image_width = $orig_width*$resize_ratio;
         $image_height = $orig_height*$resize_ratio;
         $image = imagecreatetruecolor($image_width,$image_height);

         imagecopyresampled(
               $image,$orig_image,
               0,0,0,0, # start coords
               $image_width,$image_height, # destination image width & height
               $orig_width,$orig_height # source image width & height
            );
      }
      else {
         $image = $orig_image;
      }
      return $image;
   }

   /**
    * add_new
    * @param $data
    * @param string $image_arg the input[type=file][name]
    * OR
    * @param resource $image_arg the image resource
    *
    * @deprecated use add_new_image()
    */
   public static function add_new($data/*,$image_arg*/) {
      $image_arg = func_get_arg(1);
      return static::add_new_image($data,$image_arg);
   }

   /**
    * add_new
    * @param $data
    * @param string $image_arg the input[type=file][name]
    * OR
    * @param resource $image_arg the image resource
    */
   public static function add_new_image($data,$image_arg) {
      if (!$image_arg) {
         throw new Exception("Image parameter is empty");
      }

      static::$D->StartTrans();

      $image = parent::add_new($data);

      if ($image) {
         $image_gd = self::get_gd($image_arg);
         if (!$image->save_gd($image_gd))
            static::$D->FailTrans();
      }
      else {
         throw new e_developer("Failed to insert image ".print_r($data,true));
      }

      static::$D->CompleteTrans();

      return $image;
   }


   /**
    * public save_gd($orig_image)
    *
    * @param $orig_image the gd resource of the image
    *
    */
   public function save_gd($orig_image) {
      if (!self::is_gd_resource($orig_image)) {
         throw new e_developer("\$orig_image is not gd (".(is_resource($orig_image) ? "Resource type: ".get_resource_type($orig_image) : gettype($orig_image)).")");
      }

      $image_gd = self::limit_dimension($orig_image,static::PIC_MAX_WIDTH,static::PIC_MAX_HEIGHT);

      $dir = dirname($this->file_path_name());
      if (!file_exists($dir)) {
         mkdir($dir,0777,true);
      }
      else {
         if (!is_dir($dir))
            throw new e_developer("$dir is a file instead of a directory");
      }

      if (!imagejpeg($image_gd, $this->file_path_name()))
         throw new e_developer("Failed to save image on path: ".$this->file_path_name());
      else
         return true;
   }

   /**
    * Checks if $arg is a gd resource
    *
    * @param mixed $arg the variable to check
    *
    * @since ADD MVC 0.0
    */
   static function is_gd_resource($arg) {
      return is_resource($arg) && get_resource_type($arg)==='gd';
   }
   /**
    * returns if filesize is ok
    * @param string $image_arg currently only the input[name] of the [type=file] input
    */
   static function check_file_size($image_arg) {
      if (is_string($image_arg)) {
         return $_FILES[$image_arg]['size'] <= static::PIC_MAX_FILE_SIZE;
      }
      return true;
   }
}