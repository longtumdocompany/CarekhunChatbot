<?php

// กรณีต้องการตรวจสอบการแจ้ง error ให้เปิด 3 บรรทัดล่างนี้ให้ทำงาน กรณีไม่ ให้ comment ปิดไป
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
// include composer autoload
require_once '../vendor/autoload.php';
 
// การตั้งเกี่ยวกับ bot
require_once 'bot_settings.php';
 
// กรณีมีการเชื่อมต่อกับฐานข้อมูล
//require_once("dbconnect.php");
 
//ส่วนของการเรียกใช้งาน class ผ่าน namespace
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use LINE\LINEBot\MessageBuilder\AudioMessageBuilder;
use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
use LINE\LINEBot\ImagemapActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder ;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\DatetimePickerTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder;
 
// CONNECT : เชื่อมต่อกับ LINE Messaging API
$httpClient = new CurlHTTPClient(LINE_MESSAGE_ACCESS_TOKEN);
$bot = new LINEBot($httpClient, array('channelSecret' => LINE_MESSAGE_CHANNEL_SECRET));
 
// WAIT : คำสั่งรอรับการส่งค่ามาของ LINE Messaging API
$content = file_get_contents('php://input');
 
// แปลงข้อความรูปแบบ JSON  ให้อยู่ในโครงสร้างตัวแปร array
$events = json_decode($content, true);


/*********************************1.คำถามสำหรับผู้ใช้รายใหม่************************************************* */
$txt_main_step_1_Question = "สวัสดีคะ เราคือ Carekhun : Smart Homecare and Insurance ยินดีต้อนรับผู้ใช้งานคะ 
กรุณาระบุ ชื่อ และ นามสกุล ของผู้เอาประกันภัยด้วยคะ";
$txt_main_step_2_Question  = "กรมธรรม์เลขที่อะไรคะ";
$txt_main_step_3_Question  = "วันเดือนปีเกิดของท่านคะ";
$txt_main_step_4_Question  = "ที่อยู่ปัจจุบันของท่านคะ";
$txt_main_step_5_Question  = "ที่อยู่ในการส่งเอกสาร";
$txt_main_step_6_Question  = "เบอร์โทรศัพท์ของท่านคะ";
$txt_main_step_7_Question  = "ท่านมีประกันชีวิต/ประกันสุขภาพ/ประกันอุบัติเหตุกับบริษัทอื่นหรือไม่คะ";
$txt_main_step_8_Question  = "อาชีพหรือลักษณะงานที่ท่านทำอยู่คะ";
$txt_main_step_9_Question  = "ระบุข้อมูลสุขภาพของท่านคะ";
$txt_main_step_10_Answer  = "ท่านเลือกดึงข้อมูลจาก Wearable";

/*********************************Main เลือกการทำสินไหม**************************************************/
$txt_main_question_step_1_Question = "กรุณาเลือกเมนูที่ท่านต้องการขอสินไหมมา 1 ข้อคะ";
$txt_main_question_sub_1_step_1_Question = "1.กรณีเรียกร้องสินไหมค่ารักษาพยาบาลสุขภาพหรืออุบัติเหตุ";
$txt_main_question_sub_2_step_1_Question = "2.กรณีเรียกร้องสินไหมค่าชดเชยรายวัน";
$txt_main_question_sub_3_step_1_Question = "3.กรณีเรียกร้องสินไหมทดแทนเนื่องจากอุบัติเหตุ";
$txt_main_question_sub_4_step_1_Question = "4.กรณีเรียกร้องสินไหมทุพพลภาพสิ้นเชิงถาวร";
$txt_main_question_sub_5_step_1_Question = "5.การเจ็บป่วยขั้นวิกฤต (โรคร้ายแรง) หรือโรคมะเร็ง";

/*********************************ข้อ 1. กรณีเรียกร้องสินไหมค่ารักษาพยาบาลสุขภาพหรืออุบัติเหตุ**************************************************/
$txt_Q1_step_1_Question  = "ท่านต้องการเรียกร้องค่าสินไหมในกรณีใดคะ";
$txt_Q1_step_2_Question  = "กรุณาระบุอาการเจ็บป่วยคะ";
$txt_Q1_step_3_Question  = "ระยะเวลากี่วันที่เข้ารับการรักษาคะ ";
$txt_Q1_step_4_Question  = "สถานพยาบาลที่ท่านเข้ารับการรักษาคะ";
$txt_Q1_step_5_Question  = "วันที่เข้ารับการรักษาคะ";
$txt_Q1_step_6_Question  = "อุบัติเหตุเกิดขึ้นได้อย่างไรคะ";
$txt_Q1_step_7_Question  = "ชื่อผู้เห็นเหตุการณ์คะ";
$txt_Q1_step_8_Question  = "กรุณาระบุเบอร์โทรศัพท์คะ";
$txt_Q1_step_9_Question  = "ที่อยู่ที่เกิดเหตุคะ";
$txt_Q1_step_10_Question  = "อวัยวะที่ท่านได้รับบาดเจ็บ จะถ่ายรูป หรือพิมพ์ข้อความก็ได้คะ";
$txt_Q1_step_11_Question  = "มีการแจ้งความหรือไม่คะ";
$txt_Q1_step_12_Question  = "กรุณาระบุสถานที่รับแจ้งความคะ";
$txt_Q1_step_13_Question  = "ชื่อสถานพยาบาลที่ไปรักษาคะ";
$txt_Q1_step_14_Question  = "แพทย์ผู้รักษาท่านคะ";
$txt_Q1_step_15_Question  = "วิธีการรักษา";
$txt_Q1_step_16_Question  = "ท่านมีความประสงค์จะให้บริษัทส่งผล/เช็คค่าสินไหมทดแทนไปที่ไหนคะ";
$txt_Q1_step_17_Question  = "ขอรับรองว่า ข้อความดังกล่าวข้างต้นเป็นความจริงทุกประการ";
$txt_Q1_step_18_Question  = "กรุณาแนบไฟล์เรียกร้องค่าสินไหมตามเมนูด้านล่างเลยคะ";

/*********************************ข้อ 2. คือ กรณีเรียกร้องสินไหมค่าชดเชยรายวัน**************************************************/
$txt_Q2_step_1_Question  = "วันที่เข้ารับการรักษาคือวันที่เท่าไหร่ค่ะ";
$txt_Q2_step_2_Question  = "โปรดระบุสถานที่ ที่ต้องการให้นำส่งเช็ค ค่าเรียกร้องสินไหมทดแทน (สถานที่ดังกล่าว จะต้องมีผู้เซ็นรับเอกสารกับบุรุษไปรษณีย์)";
$txt_Q2_step_3_Question  = "อะไรเป็นสาเหตุของการเกิดอาการ หรือโรคดังกล่าวคะ";
$txt_Q2_step_4_Question  = "ระยะเวลาที่นอนรักษาตัวในโรงพยาบาลกี่วันคะ";
$txt_Q2_step_5_Question  = "ท่านได้รับการวินิจฉัยอาการครั้งแรกเมื่อใดคะ";
$txt_Q2_step_6_Question  = "ครั้งสุดท้ายที่ท่านได้ไปพบแพทย์คะ";
$txt_Q2_step_7_Question  = "ระบุที่อยู่ของสถานพยาบาลที่รักษา";
$txt_Q2_step_8_Question  = "ระบุชื่อแพทย์ที่เข้ารับการรักษาคะ";
$txt_Q2_step_9_Question  = "ขอรับรองว่า ข้อความดังกล่าวข้างต้นเป็นความจริงทุกประการ";
$txt_Q2_step_10_Question  = "กรุณาแนบไฟล์เรียกร้องค่าสินไหมตามเมนูด้านล่างเลยคะ";

/*********************************ข้อ 3. คือ กรณีเรียกร้องสินไหมทดแทนเนื่องจากอุบัติเหตุ**************************************************/
$txt_Q3_step_1_Question  = "อุบัติเหตุเกิดขึ้นได้อย่างไรคะ";
$txt_Q3_step_2_Question  = "ชื่อผู้เห็นเหตุการณ์คะ";
$txt_Q3_step_3_Question  = "กรุณาระบุเบอร์โทรศัพท์คะ";
$txt_Q3_step_4_Question  = "ที่อยู่ของท่านคะ";
$txt_Q3_step_5_Question  = "อวัยวะที่ได้รับบาดเจ็บ จะถ่ายรูป หรือพิมพ์ข้อความก็ได้คะ";
$txt_Q3_step_6_Question  = "มีการแจ้งความหรือไม่";
$txt_Q3_step_7_Question  = "กรุณาระบุสถานที่รับแจ้งความคะ";
$txt_Q3_step_8_Question  = "ขอรับรองว่า ข้อความดังกล่าวข้างต้นเป็นความจริงทุกประการ";
$txt_Q3_step_9_Question  = "กรุณาแนบไฟล์เรียกร้องค่าสินไหมตามเมนูด้านล่างเลยคะ";

/*********************************ข้อ 4. คือ กรณีเรียกร้องสินไหมทุพพลภาพสิ้นเชิงถาวร**************************************************/
$txt_Q4_step_1_Question  = "วันที่ปรากฏอาการทุพพลภาพครั้งแรก เมื่อไหร่คะ";
$txt_Q4_step_2_Question  = "มีอาการอย่างไรบ้างคะ";
$txt_Q4_step_3_Question  = "สถานพยาบาลที่เข้ารับการรักษา";
$txt_Q4_step_4_Question  = "วันที่แพทย์วินิจฉัยว่าทุพพลภาพ เมื่อไหร่คะ";
$txt_Q4_step_5_Question  = "สถานที่ทำงานของท่านคะ";
$txt_Q4_step_6_Question  = "วันที่พบแพทย์ครั้งสุดท้าย เมื่อไหร่คะ";
$txt_Q4_step_7_Question  = "มีอาการอย่างไรบ้างคะ";
$txt_Q4_step_8_Question  = "สถานพยาบาลที่เข้ารับการรักษา";
$txt_Q4_step_9_Question  = "ลักษณะอาการของท่านในปัจจุบัน คือ";
$txt_Q4_sub_1_step_9_Question = "ทำกิจวัตรประจำวัน,ทำงานได้ปกติ ";
$txt_Q4_sub_2_step_9_Question = "อยู่ภายในบริเวณบ้านเท่านั้น";
$txt_Q4_sub_3_step_9_Question = "นอนอยู่บนเตียงเท่านั้น";
$txt_Q4_sub_4_step_9_Question = "อื่นๆ";
$txt_Q4_step_10_Question  = "ถ้ามีเอกสารที่ใช้ประกอบการพิจารณาเพิ่มเติม กรุณาเลือกคะ";
$txt_Q4_step_11_Question  = "ท่านมีสิทธิเรียกร้องจากบริษัทประกันอื่นๆ หรือไม่คะ";
$txt_Q4_step_12_Question  = "ขอรับรองว่า ข้อความดังกล่าวข้างต้นเป็นความจริงทุกประการ";
$txt_Q4_step_13_Question  = "กรุณาแนบไฟล์เรียกร้องค่าสินไหมตามเมนูด้านล่างเลยคะ";

/*********************************ข้อ 5. คือ การเจ็บป่วยขั้นวิกฤต (โรคร้ายแรง) หรือโรคมะเร็ง**************************************************/
$txt_Q5_step_1_Question  = "อาการที่ต้องเข้าพบแพทย์คืออะไรคะ";
$txt_Q5_step_2_Question  = "เป็นมานานเท่าไหร่แล้วคะ";
$txt_Q5_step_3_Question  = "วันที่เข้าพบแพทย์ครั้งแรก";
$txt_Q5_step_4_Question  = "ลักษณะอาการป่วยขั้นพื้นฐานและอาการป่วยที่เพิ่มขึ้นคะ";
$txt_Q5_step_5_Question  = "ท่านเคยได้รับการรักษาอาการป่วยในลักษณะเดียวกันนี้มาก่อนหรือไม่คะ";
$txt_Q5_step_6_Question  = "ท่านหรือคนในครอบครัวของท่าน เคยเป็นโรคที่มีความคล้ายคลึงหรือเกี่ยวกับอาการเจ็บป่วยดังกล่าวหรือไม่";
$txt_Q5_step_7_Question  = "โปรดระบุความสัมพันธ์ของท่านกับผู้ที่เคยประสบอาการเจ็บป่วยเหมือนท่านคะ";
$txt_Q5_step_8_Question  = "วันที่รับการวินิจฉัยคือวันที่เท่าไหร่คะ";
$txt_Q5_step_9_Question  = "ระบุอาการของท่านคะ";
$txt_Q5_step_10_Question  = "ท่านเคยมีประวัติการเรียกร้องค่าสินไหมในโรคดังกล่าวกับบริษัทประกันอื่นหรือไม่";
$txt_Q5_step_11_Question  = "ชนิดการประกันภัยที่ท่านทำไว้คะ";
$txt_Q5_step_12_Question  = "จำนวนเงินสินไหมที่ได้รับ หรือกำลังเรียกร้อง คะ";
$txt_Q5_step_13_Question  = "ท่านสูบบุหรี่หรือไม่";
$txt_Q5_step_14_Question  = "สูบปริมาณเท่าใดต่อวัน";
$txt_Q5_step_15_Question  = "สูบมานานเท่าใด";
$txt_Q5_step_16_Question  = "ขอรับรองว่า ข้อความดังกล่าวข้างต้นเป็นความจริงทุกประการ";
$txt_Q5_step_17_Question  = "กรุณาแนบไฟล์เรียกร้องค่าสินไหมตามลิงค์ด้านล่างเลยคะ";

//**************************************/Business Logic********************************************************

$stateQuestionsForNewUserFlag = -1;
$stateMainSelectedClaim = -1;
$stateClaimsForMedicalExpensesHealthOrAccident = -1;   
$stateClaimsForDailyCompensation = -1;    
$stateAccidentClaims = -1;               
$stateClaimsForPermanentDisability = -1;    
$stateCriticalIllnessSeriousIllnessOrCancer = -1;                                                                              

if(!is_null($events)){

    // ถ้ามีค่า สร้างตัวแปรเก็บ replyToken ไว้ใช้งาน
    $replyToken = $events['events'][0]['replyToken'];
    $typeMessage = $events['events'][0]['message']['type'];
    $userMessage = $events['events'][0]['message']['text'];

    //1.คำถามสำหรับผู้ใช้รายใหม่
    callBusinessLogic($typeMessage);
}

function callBusinessLogic($typeMessage){
    //1.คำถามสำหรับผู้ใช้รายใหม่
    questionsForNewUser($typeMessage);
}

//1
function questionsForNewUser($typeMessage){
    switch ($typeMessage){
        case 'text':
            switch ($stateQuestionsForNewUserFlag) {                                         //User พิมพ์เข้ามา
                case "0":
                    $textReplyMessage = $txt_main_step_2_Question;
                    $stateQuestionsForNewUserFlag = 1;
                    break;
                case "1":
                    $textReplyMessage = $txt_main_step_3_Question;
                    $stateQuestionsForNewUserFlag = 2;
                    break;
                case "2":
                    $textReplyMessage = $txt_main_step_4_Question;
                    $stateQuestionsForNewUserFlag = 3;
                    break;
                case "3":
                    $textReplyMessage = $txt_main_step_5_Question;
                    $stateQuestionsForNewUserFlag = 4;
                    break;
                case "4":
                    $textReplyMessage = $txt_main_step_6_Question;
                    $stateQuestionsForNewUserFlag = 5;
                    break;
                case "5":
                    $textReplyMessage = $txt_main_step_7_Question;
                    $stateQuestionsForNewUserFlag = 6;
                    break;
                case "6":
                    $textReplyMessage = $txt_main_step_8_Question;
                    $stateQuestionsForNewUserFlag = 7;
                    break;
                case "7":
                    $textReplyMessage = $txt_main_step_9_Question;
                    $stateQuestionsForNewUserFlag = 8;
                    break;
                case "8":
                    $textReplyMessage = $txt_main_step_10_Question;         
                    $stateQuestionsForNewUserFlag = 9;

                    //2.Main เลือกการทำสินไหม
                    mainSelectedClaim($typeMessage);                            //จบ

                    break;
                default:
                    $textReplyMessage = "กรุณาทวนคำตอบของคุณใหม่อีกครั้งคะ";
                    break;                                      
            }
            break;
        default:
            $textReplyMessage = $txt_main_step_1_Question;
            $stateQuestionsForNewUserFlag = 0;
            break;  
    }
}

//2
function mainSelectedClaim($typeMessage){
    switch ($typeMessage){
        case 'text':
            switch ($userMessage) {                                                 //User พิมพ์เข้ามา
                case "0":
                    $textReplyMessage = $txt_main_question_sub_1_step_1_Question;
                    $stateMainSelectedClaim = 1;
                    claimsForMedicalExpensesHealthOrAccident($typeMessage);         //3. ข้อ 1. กรณีเรียกร้องสินไหมค่ารักษาพยาบาลสุขภาพหรืออุบัติเหตุ
                    break;
                case "1":
                    $textReplyMessage = $txt_main_question_sub_2_step_2_Question;
                    $stateMainSelectedClaim = 2;
                    claimsForDailyCompensation($typeMessage);                       //4. ข้อ 2. คือ กรณีเรียกร้องสินไหมค่าชดเชยรายวัน
                    break;
                case "2":
                    $textReplyMessage = $txt_main_question_sub_3_step_3_Question;
                    $stateMainSelectedClaim = 3;
                    accidentClaims($typeMessage);                                   //5. ข้อ 3. คือ กรณีเรียกร้องสินไหมทดแทนเนื่องจากอุบัติเหตุ
                    break;
                case "3":
                    $textReplyMessage = $txt_main_question_sub_4_step_4_Question;
                    $stateMainSelectedClaim = 4;
                    claimsForPermanentDisability($typeMessage);                      //6. ข้อ 4. คือ กรณีเรียกร้องสินไหมทุพพลภาพสิ้นเชิงถาวร
                    break;
                case "4":
                    $textReplyMessage = $txt_main_question_sub_5_step_5_Question;
                    $stateMainSelectedClaim = 5;
                    criticalIllnessSeriousIllnessOrCancer($typeMessage);             //7. ข้อ 5. คือ การเจ็บป่วยขั้นวิกฤต (โรคร้ายแรง) หรือโรคมะเร็ง
                    break;
                default:
                    $textReplyMessage = "ท่านไม่ได้เลือกเมนู กรุณาเลือกเมนูที่ต้องการทำสินไหมด้วยคะ";
                    break;                                      
            }
            break;
        default:
            $textReplyMessage = $txt_main_question_step_1_Question;
            $stateMainSelectedClaim = 0;
            break;  
    }
}

//3
function claimsForMedicalExpensesHealthOrAccident($typeMessage){
    switch ($typeMessage){
        case 'text':
            switch ($userMessage) {                                                     //User พิมพ์เข้ามา
                case "0":
                    $textReplyMessage = $txt_Q1_step_2_Question;
                    $stateClaimsForMedicalExpensesHealthOrAccident = 1;
                    break;
                case "1":
                    $textReplyMessage = $txt_Q1_step_3_Question;
                    $stateClaimsForMedicalExpensesHealthOrAccident = 2;
                    break;
                case "2":
                    $textReplyMessage = $txt_Q1_step_4_Question;
                    $stateClaimsForMedicalExpensesHealthOrAccident = 3;
                    break;
                case "3":
                    $textReplyMessage = $txt_Q1_step_5_Question;
                    $stateClaimsForMedicalExpensesHealthOrAccident = 4;
                    break;
                case "4":
                    $textReplyMessage = $txt_Q1_step_6_Question;
                    $stateClaimsForMedicalExpensesHealthOrAccident = 5;
                    break;
                case "5":
                    $textReplyMessage = $txt_Q1_step_7_Question;
                    $stateClaimsForMedicalExpensesHealthOrAccident = 6;
                    break;
                case "6":
                    $textReplyMessage = $txt_Q1_step_8_Question;
                    $stateClaimsForMedicalExpensesHealthOrAccident = 7;
                    break;
                case "7":
                    $textReplyMessage = $txt_Q1_step_9_Question;
                    $stateClaimsForMedicalExpensesHealthOrAccident = 8;
                    break;
                case "8":
                    $textReplyMessage = $txt_Q1_step_10_Question;
                    $stateClaimsForMedicalExpensesHealthOrAccident = 9;
                    break;
                case "9":
                    $textReplyMessage = $txt_Q1_step_11_Question;
                    $stateClaimsForMedicalExpensesHealthOrAccident = 10;
                    break;
                case "10":
                    $textReplyMessage = $txt_Q1_step_12_Question;
                    $stateClaimsForMedicalExpensesHealthOrAccident = 11;
                    break;
                case "11":
                    $textReplyMessage = $txt_Q1_step_13_Question;
                    $stateClaimsForMedicalExpensesHealthOrAccident = 12;
                    break;
                case "12":
                    $textReplyMessage = $txt_Q1_step_14_Question;
                    $stateClaimsForMedicalExpensesHealthOrAccident = 13;
                    break;
                case "13":
                    $textReplyMessage = $txt_Q1_step_15_Question;
                    $stateClaimsForMedicalExpensesHealthOrAccident = 14;
                    break;
                case "14":
                    $textReplyMessage = $txt_Q1_step_16_Question;
                    $stateClaimsForMedicalExpensesHealthOrAccident = 15;
                    break;
                case "15":
                    $textReplyMessage = $txt_Q1_step_17_Question;
                    $stateClaimsForMedicalExpensesHealthOrAccident = 16;
                    break;
                case "16":
                    $textReplyMessage = $txt_Q1_step_18_Question;
                    //Link ไปที่ Web
                    break;
                default:
                    $textReplyMessage = "กรุณาทวนคำตอบใหม่อีกรอบคะ";
                    break;                                      
            }
            break;
        default:
            $textReplyMessage = $txt_Q1_step_1_Question;
            $stateMainSelectedClaim = 0;
            break;  
    }
}

//4
function claimsForDailyCompensation($typeMessage){
    switch ($typeMessage){
        case 'text':
            switch ($userMessage) {                                         //User พิมพ์เข้ามา
                case "0":
                    $textReplyMessage = $txt_Q2_step_2_Question;
                    $stateClaimsForDailyCompensation = 1;
                    break;
                case "1":
                    $textReplyMessage = $txt_Q2_step_3_Question;
                    $stateClaimsForDailyCompensation = 2;
                    break;
                case "2":
                    $textReplyMessage = $txt_Q2_step_4_Question;
                    $stateClaimsForDailyCompensation = 3;
                    break;
                case "3":
                    $textReplyMessage = $txt_Q2_step_5_Question;
                    $stateClaimsForDailyCompensation = 4;
                    break;
                case "4":
                    $textReplyMessage = $txt_Q2_step_6_Question;
                    $stateClaimsForDailyCompensation = 5;
                    break;
                case "5":
                    $textReplyMessage = $txt_Q2_step_7_Question;
                    $stateClaimsForDailyCompensation = 6;
                    break;
                case "6":
                    $textReplyMessage = $txt_Q2_step_8_Question;
                    $stateClaimsForDailyCompensation = 7;
                    break;
                case "7":
                    $textReplyMessage = $txt_Q2_step_9_Question;
                    $stateClaimsForDailyCompensation = 8;
                    break;
                case "8":
                    $textReplyMessage = $txt_Q2_step_10_Question;
                    //Link ไปที่ Web
                    break;
                default:
                    $textReplyMessage = "กรุณาทวนคำตอบใหม่อีกรอบคะ";
                    break;                                      
            }
            break;
        default:
            $textReplyMessage = $txt_Q2_step_1_Question;
            $stateClaimsForDailyCompensation = 0;
            break;  
    }
}

//5
function accidentClaims($typeMessage){
    switch ($typeMessage){
        case 'text':
            switch ($userMessage) {                                             //User พิมพ์เข้ามา
                case "0":
                    $textReplyMessage = $txt_Q3_step_2_Question;
                    $stateAccidentClaims = 1;
                    break;
                case "1":
                    $textReplyMessage = $txt_Q3_step_3_Question;
                    $stateAccidentClaims = 2;
                    break;
                case "2":
                    $textReplyMessage = $txt_Q3_step_4_Question;
                    $stateAccidentClaims = 3;
                    break;
                case "3":
                    $textReplyMessage = $txt_Q3_step_5_Question;
                    $stateAccidentClaims = 4;
                    break;
                case "4":
                    $textReplyMessage = $txt_Q3_step_6_Question;
                    $stateAccidentClaims = 5;
                    break;
                case "5":
                    $textReplyMessage = $txt_Q3_step_7_Question;
                    $stateAccidentClaims = 6;
                    break;
                case "6":
                    $textReplyMessage = $txt_Q3_step_8_Question;
                    $stateAccidentClaims = 7;
                    break;
                case "7":
                    $textReplyMessage = $txt_Q3_step_9_Question;
                    break;
                default:
                    $textReplyMessage = "กรุณาทวนคำตอบใหม่อีกรอบคะ";
                    break;                                      
            }
            break;
        default:
            $textReplyMessage = $txt_Q3_step_1_Question;
            $stateAccidentClaims = 0;
            break;  
    }
}

//6
function claimsForPermanentDisability($typeMessage){
    switch ($typeMessage){
        case 'text':
            switch ($userMessage) {                                         //User พิมพ์เข้ามา
                case "0":
                    $textReplyMessage = $txt_Q4_step_2_Question;
                    $stateClaimsForPermanentDisability = 1;
                    break;
                case "1":
                    $textReplyMessage = $txt_Q4_step_3_Question;
                    $stateClaimsForPermanentDisability = 2;
                    break;
                case "2":
                    $textReplyMessage = $txt_Q4_step_4_Question;
                    $stateClaimsForPermanentDisability = 3;
                    break;
                case "3":
                    $textReplyMessage = $txt_Q4_step_5_Question;
                    $stateClaimsForPermanentDisability = 4;
                    break;
                case "4":
                    $textReplyMessage = $txt_Q4_step_6_Question;
                    $stateClaimsForPermanentDisability = 5;
                    break;
                case "5":
                    $textReplyMessage = $txt_Q4_step_7_Question;
                    $stateClaimsForPermanentDisability = 6;
                    break;
                case "6":
                    $textReplyMessage = $txt_Q4_step_8_Question;
                    $stateClaimsForPermanentDisability = 7;
                    break;
                case "7":
                    $textReplyMessage = $txt_Q4_step_9_Question;
                    $stateClaimsForPermanentDisability = 8;
                    break;
                case "8":
                    $textReplyMessage = $txt_Q4_step_10_Question;

                    /*  $txt_Q4_sub_1_step_9_Question = "ทำกิจวัตรประจำวัน,ทำงานได้ปกติ ";
                        $txt_Q4_sub_2_step_9_Question = "อยู่ภายในบริเวณบ้านเท่านั้น";
                        $txt_Q4_sub_3_step_9_Question = "นอนอยู่บนเตียงเท่านั้น";
                        $txt_Q4_sub_4_step_9_Question = "อื่นๆ";    */

                        $stateClaimsForPermanentDisability = 9;

                    break;
                case "9":
                    $textReplyMessage = $txt_Q4_step_11_Question;
                    $stateClaimsForPermanentDisability = 10;
                    break;
                case "10":
                    $textReplyMessage = $txt_Q4_step_12_Question;
                    $stateClaimsForPermanentDisability = 11;
                    break;
                case "11":
                    $textReplyMessage = $txt_Q4_step_13_Question;
                    break;
                default:
                    $textReplyMessage = "กรุณาทวนคำตอบใหม่อีกรอบคะ";
                    break;                                      
            }
            break;
        default:
            $textReplyMessage = $txt_Q4_step_1_Question;
            $stateClaimsForPermanentDisability = 0;
            break;  
    }
}

//7
function criticalIllnessSeriousIllnessOrCancer($typeMessage){
    switch ($typeMessage){
        case 'text':
            switch ($userMessage) {                                         //User พิมพ์เข้ามา
                case "0":
                    $textReplyMessage = $txt_Q5_step_2_Question;
                    $stateCriticalIllnessSeriousIllnessOrCancer = 1;
                    break;
                case "1":
                    $textReplyMessage = $txt_Q5_step_3_Question;
                    $stateCriticalIllnessSeriousIllnessOrCancer = 2;
                    break;
                case "2":
                    $textReplyMessage = $txt_Q5_step_4_Question;
                    $stateCriticalIllnessSeriousIllnessOrCancer = 3;
                    break;
                case "3":
                    $textReplyMessage = $txt_Q5_step_5_Question;
                    $stateCriticalIllnessSeriousIllnessOrCancer = 4;
                    break;
                case "4":
                    $textReplyMessage = $txt_Q5_step_6_Question;
                    $stateCriticalIllnessSeriousIllnessOrCancer = 5;
                    break;
                case "5":
                    $textReplyMessage = $txt_Q5_step_7_Question;
                    $stateCriticalIllnessSeriousIllnessOrCancer = 6;
                    break;
                case "6":
                    $textReplyMessage = $txt_Q5_step_8_Question;
                    $stateCriticalIllnessSeriousIllnessOrCancer = 7;
                    break;
                case "7":
                    $textReplyMessage = $txt_Q5_step_9_Question;
                    $stateCriticalIllnessSeriousIllnessOrCancer = 8;
                    break;
                case "8":
                    $textReplyMessage = $txt_Q5_step_10_Question;
                    $stateCriticalIllnessSeriousIllnessOrCancer = 9;
                    break;
                case "9":
                    $textReplyMessage = $txt_Q5_step_11_Question;
                    $stateCriticalIllnessSeriousIllnessOrCancer = 10;
                    break;
                case "10":
                    $textReplyMessage = $txt_Q5_step_12_Question;
                    $stateCriticalIllnessSeriousIllnessOrCancer = 11;
                    break;
                case "11":
                    $textReplyMessage = $txt_Q5_step_13_Question;
                    $stateCriticalIllnessSeriousIllnessOrCancer = 12;
                    break;
                case "12":
                    $textReplyMessage = $txt_Q5_step_14_Question;
                    $stateCriticalIllnessSeriousIllnessOrCancer = 13;
                    break;
                case "13":
                    $textReplyMessage = $txt_Q5_step_15_Question;
                    $stateCriticalIllnessSeriousIllnessOrCancer = 14;
                    break;
                case "14":
                    $textReplyMessage = $txt_Q5_step_16_Question;
                    $stateCriticalIllnessSeriousIllnessOrCancer = 15;
                    break;
                case "15":
                    $textReplyMessage = $txt_Q5_step_17_Question;
                    //Link ไปที่ Web
                    break;
                default:
                    $textReplyMessage = "กรุณาทวนคำตอบใหม่อีกรอบคะ";
                    break;                                      
            }
            break;
        default:
            $textReplyMessage = $txt_Q5_step_1_Question;
            $stateCriticalIllnessSeriousIllnessOrCancer = 0;
            break;  
    }
}

//เก็บข้อมูลจาก Firebase เพื่อมาโชว์ใน Line
function callDatabase(){
    echo "write firebase database";
}


// SEND : ส่วนของคำสั่งจัดเตียมรูปแบบข้อความสำหรับส่ง
$textMessageBuilder = new TextMessageBuilder(json_encode($events));
 
// FEEDBACK : ส่วนของคำสั่งตอบกลับข้อความ
$response = $bot->replyMessage($replyToken,$textMessageBuilder);
if ($response->isSucceeded()) {
    echo 'Succeeded!';
    return;
}
 
// Failed
echo $response->getHTTPStatus() . ' ' . $response->getRawBody();

//****************************************Util****************************************************** */
function lineUtils($typeMessage){
    switch ($typeMessage){
        case 'text':
            switch ($userMessage) {
                case "img_claimsForMedicalExpensesHealthOrAccident":        //ขอแบบฟอร์ม กรณีเรียกร้องสินไหมค่ารักษาพยาบาลสุขภาพหรืออุบัติเหตุ
                    $picFullSize = 'https://www.mywebsite.com/imgsrc/photos/f/simpleflower';
                    $picThumbnail = 'https://www.mywebsite.com/imgsrc/photos/f/simpleflower/240';
                    $replyData = new ImageMessageBuilder($picFullSize,$picThumbnail);
                    break;
                case "img_claimsForDailyCompensation":                      //ขอแบบฟอร์ม กรณีเรียกร้องสินไหมค่าชดเชยรายวัน
                    $picFullSize = 'https://www.mywebsite.com/imgsrc/photos/f/simpleflower';
                    $picThumbnail = 'https://www.mywebsite.com/imgsrc/photos/f/simpleflower/240';
                    $replyData = new ImageMessageBuilder($picFullSize,$picThumbnail);
                    break;
                case "img_accidentClaims":                                  //ขอแบบฟอร์ม กรณีเรียกร้องสินไหมทดแทนเนื่องจากอุบัติเหตุ
                    $picFullSize = 'https://www.mywebsite.com/imgsrc/photos/f/simpleflower';
                    $picThumbnail = 'https://www.mywebsite.com/imgsrc/photos/f/simpleflower/240';
                    $replyData = new ImageMessageBuilder($picFullSize,$picThumbnail);
                    break;
                case "img_claimsForPermanentDisability":                    //ขอแบบฟอร์ม กรณีเรียกร้องสินไหมทุพพลภาพสิ้นเชิงถาวร
                    $picFullSize = 'https://www.mywebsite.com/imgsrc/photos/f/simpleflower';
                    $picThumbnail = 'https://www.mywebsite.com/imgsrc/photos/f/simpleflower/240';
                    $replyData = new ImageMessageBuilder($picFullSize,$picThumbnail);
                    break;
                case "img_criticalIllnessSeriousIllnessOrCancer":           //ขอแบบฟอร์ม การเจ็บป่วยขั้นวิกฤต (โรคร้ายแรง) หรือโรคมะเร็ง
                    $picFullSize = 'https://www.mywebsite.com/imgsrc/photos/f/simpleflower';
                    $picThumbnail = 'https://www.mywebsite.com/imgsrc/photos/f/simpleflower/240';
                    $replyData = new ImageMessageBuilder($picFullSize,$picThumbnail);
                    break;

                //วิดีโอ
                case "video":
                    $picThumbnail = 'https://www.mywebsite.com/imgsrc/photos/f/sampleimage/240';
                    $videoUrl = "https://www.mywebsite.com/simplevideo.mp4";                
                    $replyData = new VideoMessageBuilder($videoUrl,$picThumbnail);
                    break;

                //เสียง
                case "audio":
                    $audioUrl = "https://www.mywebsite.com/simpleaudio.mp3";
                    $replyData = new AudioMessageBuilder($audioUrl,27000);
                    break;

                case "location":
                    $placeName = "ที่ตั้งโรงพยาบาล";
                    $placeAddress = "โรงพยาบาลเลิดสิน";
                    $latitude = 13.780401863217657;
                    $longitude = 100.61141967773438;
                    $replyData = new LocationMessageBuilder($placeName, $placeAddress, $latitude ,$longitude);              
                    break;
                
                //sticker    
                case "s":
                    $stickerID = 22;
                    $packageID = 2;
                    $replyData = new StickerMessageBuilder($packageID,$stickerID);
                    break; 

                case "image_builder":
                    $imageMapUrl = 'https://www.mywebsite.com/imgsrc/photos/w/sampleimagemap';
                    $replyData = new ImagemapMessageBuilder(
                        $imageMapUrl,
                        'This is Title',
                        new BaseSizeBuilder(699,1040),
                        array(
                            new ImagemapMessageActionBuilder(
                                'test image map',
                                new AreaBuilder(0,0,520,699)
                                ),
                            new ImagemapUriActionBuilder(
                                'http://www.ninenik.com',
                                new AreaBuilder(520,0,520,699)
                                )
                        )); 
                    break; 

                case "template_message_builder":
                    $replyData = new TemplateMessageBuilder('Confirm Template',
                        new ConfirmTemplateBuilder(
                                'Confirm template builder',
                                array(
                                    new MessageTemplateActionBuilder(
                                        'Yes',
                                        'Text Yes'
                                    ),
                                    new MessageTemplateActionBuilder(
                                        'No',
                                        'Text NO'
                                    )
                                )
                        )
                    );
                    break;  

                default:
                    $textReplyMessage = " คุณไม่ได้พิมพ์ตามที่กำหนด";
                    $replyData = new TextMessageBuilder($textReplyMessage);         
                    break;                                      
            }
            break;
        default:
        $textReplyMessage = json_encode($events);
        $replyData = new TextMessageBuilder($textReplyMessage);         
        break;  
        }
    }

?>