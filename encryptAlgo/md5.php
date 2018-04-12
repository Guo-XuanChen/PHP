<?php
/*
    * 參考網址: https://zh.wikipedia.org/wiki/MD5, https://baike.baidu.com/item/MD5
    
    * 創建時間: 2018/02/10 18:17

    * 創作作者: Guo, Xuan-Chen
*/

Class MD5{
    
    public $plainText;

    public function __construct($plainText){
        $this->plainText = $plainText;
        $this->main();
    }


    /* 運算副函式 */
    private function F($x, $y, $z){
        return ($x & $y) | ((~$x) & $z);
    }

    private function G($x, $y, $z){
        return ($x & $z) | ($y & (~$z));
    }

    private function H($x, $y, $z){
        return $x ^ $y ^ $z;    
    }

    private function I($x, $y, $z){
        return $y ^ ( $x | (~$z));
    }

    /* shift  循環左移 */
    private function leftrotate($x, $c){
        return ($x << $c) | $x >> (32 - $c);
    }


    /* 開始 MD5 運算 */
    private function md5($A, $B, $C, $D, array $Y){

	/*
	    s specifies the per-round shift amounts
	    向左移數
	*/
        $s = array();
        $s = [
            7, 12, 17, 22, 7, 12, 17, 22, 7, 12, 17, 22, 7, 12, 17, 22,
            5, 9, 14, 20, 5, 9, 14, 20, 5, 9, 14, 20, 5, 9, 14, 20,
            4, 11, 16, 23, 4, 11, 16, 23, 4, 11, 16, 23, 4, 11, 16, 23,
            6, 10, 15, 21, 6, 10, 15, 21, 6, 10, 15, 21, 6, 10, 15, 21
        ];

        /*
            Use binary integer part of the sines of integers as constants
            定義常數 (非線性)
        */
        $k = array();
        for($x=1; $x<=64; $x++){
            $k[$x-1] = floor((pow(2,32) * abs(sin($x))));
        }

        /*  initialize block (32 bits = 4 bytes, range 0 ~ 15 (16) Group) */
        $_M = array_chunk($Y, 4);
        
        for($x=0; $x<16; $x++){
            $buffer = pack("CCCC",$_M[$x][0], $_M[$x][1], $_M[$x][2], $_M[$x][3]);
            $M[$x] = unpack("V",$buffer)[1];   
        }

    
        for($x=0; $x<64; $x++){
            $f = 0; $g = 0;
            
            if($x < 16){
                $f = $this->F($B, $C, $D);
                $g = $x;
            }else if($x < 32){
                $f = $this->G($B, $C, $D);
                $g = (5 * $x + 1) & 0xf; // mod 16
            }else if($x < 48){
                $f = $this->H($B, $C, $D);
                $g = (3 * $x + 5) & 0xf; // mod 16
            }else{
                $f = $this->I($B, $C, $D);
                $g = (7 * $x) & 0xf;     // mod 16
            }

            $f = ($A + $f + $k[$x] + $M[$g]) & 0xffffffff;
            $f = $this->leftrotate($f, $s[$x]);
            
            list($A, $B, $C, $D) = array($D, ($B + $f) & 0xffffffff, $B, $C);

        }
        return array($A, $B, $C, $D);
    }

    /* 顯示 MD5 過後的文字 */
    private function md5_printf($A, $B, $C, $D){
        // little-endian
        list($A, $B, $C, $D) = array(pack("V", $A), pack("V", $B), pack("V", $C), pack("V", $D));
        // big-endian
        list($A, $B, $C, $D) = array(unpack("N",$A)[1],unpack("N",$B)[1],unpack("N",$C)[1],unpack("N",$D)[1]);
        // 印出
        printf("<h1>%08x%08x%08x%08x</h1>",$A, $B, $C, $D);
    }
    
    /* main function */
    private function main(){
        // string length
        $plainText_Length = strlen($this->plainText) * 8;

        /*
	    string buffer
	    each 8 bits (1 byte) to array
	*/
        $plainText_Buffer = unpack("C*", $this->plainText);

        /*
            Append '1'  to  plainText
            1000 0000 (一個1，後面補零)
        */
        array_push($plainText_Buffer, 0x80);

        /* Append '0' padding to plainText */
        while(true){
            /*
				8 bytes (64bits) 為預留給存放 Length Information
				算式為 N * 512 + 448 + 64 = (N + 1) * 512
			*/
            if(0 === (count($plainText_Buffer) + 8) % 64){
                break;
            }
            array_push($plainText_Buffer, 0x00);
        }

        /*
            Append bit length of message as 64-bit 
            little-endian integer to message
            pack to string, unpack to binary
        */
        $buffer = pack("P", $plainText_Length);
        $buffer = unpack("C*",$buffer);
        $plainText_Buffer = array_merge($plainText_Buffer, $buffer);

        /* clear variable */
        unset($buffer);

        /* Initialize chunk */
        $plainText_Chunk = array_chunk($plainText_Buffer, 64);
        $plainText_ChunkCount = count($plainText_Chunk);

        /* Initialize variables (IV) */
        $A = 0x67452301;
        $B = 0xefcdab89;
        $C = 0x98badcfe;
        $D = 0x10325476;

        for($x=0; $x<$plainText_ChunkCount; $x++){
            list($An, $Bn, $Cn, $Dn) = $this->md5($A, $B, $C, $D, $plainText_Chunk[$x]);
            //固定32bits
            $A = ($A + $An) & 0xffffffff;
            $B = ($B + $Bn) & 0xffffffff;
            $C = ($C + $Cn) & 0xffffffff;
            $D = ($D + $Dn) & 0xffffffff; 
        }

        $this->md5_printf($A, $B, $C, $D);
    }
}
$password = $_GET['password'];
$test = new MD5($password);
