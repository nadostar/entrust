<?php
/**
 * CSV 출력 클래스
 *
 * @package    fw.core.output
 */

require_once __DIR__.'/_Output.php';

class Output_CSV extends _Output {
    
    /** エンコード設定 */
    const SRC_ENCODING = 'UTF-8';
    const DST_ENCODING = 'UTF-8';
    
    /*行カウント*/
    private $row_cnt=0;
    
    /*出力ファイル名*/
    private $export_file_name='Output_CSV.csv';
    
    /*output_stream*/
    private $output_stream=null;
    
    /*アサインと同時に即時出力する*/
    private $realtime_output=false;
    
    
    //エクスポートファイル名指定
    public function setExportFileName($filename) {
        $this->export_file_name = $filename;
        
        //open file pointer to standard output
        //$fp = fopen('php://output', 'w');
        
        //add BOM to fix UTF-8 in Excel
        //fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
    }
    
    
    //データセット
    public function assignRow($value) {
        $this->assign($this->row_cnt, $value);
        $this->row_cnt++;
        if( $this->realtime_output ) {
            $this->outputRow($value);
        }
    }
    
    
    //アクションの処理終わりまで待てない際にコールする
    public function realtimeOutput() {
        if( is_null($this->output_stream) ) {
            //バッファにため込まないのでヘッダーはtext/plainにしない
            $this->realtime_output=true;
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.$this->export_file_name);
            $this->output_stream = fopen('php://output', 'w');
        }
    }
    
    
    private function outputRow($buff) {
        
        //配列じゃなければ配列にする
        $out_buff;
        if(is_array($buff)) {
            $out_buff = $buff;
        } else {
            $out_buff = array($buff);
        }
        
        //リアルタイムで出力しているので出力データは都度エンコードする
        if( $this->realtime_output ) {
            mb_convert_variables(self::DST_ENCODING, self::SRC_ENCODING, $out_buff);
        }
        
        //出力
        fputcsv($this->output_stream, $out_buff);
    }
    
    
    /**
     * CSV出力を行う。
     */
    public function output() {
        if( $this->realtime_output ) {
            //リアルタイム出力なので、すること無し
            return;
        }
        
        try {
            //csv出力をobに貯める
            ob_start();
            header('Content-Type: text/plain;charset=UTF-8');
            $this->output_stream = fopen('php://output', 'w');
            
            foreach($this->data as $buff ) {
                $this->outputRow($buff);
            }
            
            //貯めたobをエンコーディングして出力
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.$this->export_file_name);
            echo mb_convert_encoding(ob_get_clean(), self::DST_ENCODING, self::SRC_ENCODING );
        } catch (Exception $e) {
            throw new Exception_Output('failed output csv');
        }
    }
}
