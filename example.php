$htmlEntityDecoder = new HTMLEntityDecoder('language.vfx');
print_r($htmlEntityDecoder->render());
echo $htmlEntityDecoder->get('bengali');
echo $htmlEntityDecoder->get('chinese');
