<?php
/*

 * 參考網址: http://blog.csdn.net/blakegao/article/details/11022827

 * 創建時間: 2018/02/07 00:56

 * 創作作者: Guo,Xuan-Chen

*/

Class CiscoType7 
{
    //ASCII Table
    private $dictionary;
    //encrypt password
    public $encrypt_password;
    //decrypt password
    public $decrypt_password;

    public function __construct()
    {
        //初始化變數
        $this->dictionary = [
            0x64, 0x73, 0x66, 0x64, 0x3b, 0x6b, 0x66, 0x6f,
            0x41, 0x2c, 0x2e, 0x69, 0x79, 0x65, 0x77, 0x72,
            0x6b, 0x6c, 0x64, 0x4a, 0x4b, 0x44, 0x48, 0x53 , 0x55, 0x42
        ];
    }

    public function encryption()
    {
        printf("<h1>這是一個加密function</h1>");
        printf("您輸入的密碼是: %s <br>",$this->encrypt_password);

        //Random Seed 0 ~ 25
        $seed = rand(0,25);
        $dictionary = $this->dictionary;

        //將密碼分割給陣列
        $encrypt_array = str_split($this->encrypt_password, 1);
        //存放加密過後的字元(十六進制)
        $encrypt_buffer = array();
        //buffer 的位置偏移
        $encrypt_count = 1;
        //計算密碼的長度
        $encrypt_length = count($encrypt_array);
        //暫存目前加密過的資料
        $encrypt_char = '';
        //dictionary 的位置偏移
        $position = 0;
        

        $encrypt_buffer[0] = sprintf("%02d", $seed);      //首兩位為 seed 亂數

        //開始加密
        for($x=0; $x<$encrypt_length; $x++,$seed++)
        {
            //不能大於25，而0~25的位置
            $position = $seed % 25;
            //XOR Encryption,
            //ord 轉成 ASCII 碼
            $encrypt_char = $dictionary[$position] ^ ord($encrypt_array[$x]);
            //以16進位顯示
            $encrypt_buffer[$encrypt_count++] = sprintf("%02x", $encrypt_char);
        }

        //將 buffer 轉成文字，輸出密碼
        printf("您的密碼，加密過後: %s <br>",implode('', $encrypt_buffer));
    }

    public function decryption()
    {
        printf("<h1>這是一個解密function</h1>");
        printf("您輸入的加密密碼為: %s <br/>", $this->decrypt_password);

        $dictionary = $this->dictionary;

        //計算加密字串的長度
        $decrypt_password_length = strlen($this->decrypt_password);
        //取得 seed 亂數值
        $decrypt_seed = (int)(substr($this->decrypt_password, 0, 2));
        //將加密字串取掉seed後取得data,以兩個(hex)為單位進行切割，放置陣列
        $decrypt_array = str_split(substr($this->decrypt_password, 2, $decrypt_password_length),2);
        //存放解密後的資料
        $decrypt_buffer = array();
        //buffer 的位置偏移量
        $decrypt_count = 0;
        //計算分割後有幾個需要解密的字
        $decrypt_length = count($decrypt_array);

        //dictionary 的位置偏移量
        $position = 0;

        for($x=0; $x<$decrypt_length; $x++,$decrypt_seed++)
        {
            $position = $decrypt_seed % 25;
            //hexdec如果字串是十六進制，直接轉換
             //XOR進行解密
            $decrypt_char = $dictionary[$position] ^ hexdec($decrypt_array[$x]);
             //ASCII轉成字串，傳回陣列
            $decrypt_buffer[$decrypt_count++] = chr($decrypt_char);
        }

         //將 buffer 轉成文字，輸出明文
        printf("您的加密密碼，解密後為 %s<br>",implode('', $decrypt_buffer));
    }
}
$test = new CiscoType7();
$test->encrypt_password = "cisco";
$test->encryption();
$test->decrypt_password = "047122331C320A0B4C4A4F295421262F0D030E";
$test->decryption();
