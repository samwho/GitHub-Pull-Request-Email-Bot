<?php
require_once dirname(__FILE__) . '/tests.init.php';

class TestOfTemplateParser extends UnitTestCase {
    public function testParse() {
        $test_parse_array = array(
            'test_variable' => 'This is only a test.'
        );

        $output = TemplateParser::parse('test_template_do_not_edit.tpl', null, $test_parse_array);

        $this->assertPattern('/This is only a test\./', $output);
    }
}
