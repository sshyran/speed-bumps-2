<?php

class Test_Speed_Bumps_Filter_Usage extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->speed_bumps = Speed_Bumps();
	}

	public function tearDown() {
		parent::tearDown();
		$this->speed_bumps->clear_speed_bump( 'speed_bump_test' );
		$this->speed_bumps->clear_speed_bump( 'rickroll' );
	}

	public function test_speed_bump_filter_usage() {
		register_speed_bump( 'speed_bump_test', array(
			'string_to_inject' => function() { return '<div id="speed-bump-test"></div>'; },
			'minimum_content_length' => false,
			'from_start' => false,
			'from_end' => false,
		));
		add_filter( 'the_content', 'insert_speed_bumps' );
		$post_id = $this->factory->post->create( array( 'post_content' => $this->get_dummy_content() ) );
		$post = get_post( $post_id );
		$this->assertContains( '<div id="speed-bump-test"></div>', apply_filters( 'the_content', $post->post_content ) );
	}

	public function test_rickroll_example_with_filter() {
		register_speed_bump( 'rickroll', array(
			'string_to_inject' => function() { return '<iframe width="420" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe>'; },
			'from_start' => 2,
			'from_end' => false,
			'from_element' => false,
			'minimum_content_length' => array( 'characters' => 1200 ),
		) );
		add_filter( 'the_content', 'insert_speed_bumps' );
		$post_id = $this->factory->post->create( array( 'post_content' => $this->get_dummy_content() ) );
		$post = get_post( $post_id );
		$this->assertSpeedBumpAtParagraph( apply_filters( 'the_content', $post->post_content ), 4, '<iframe width="420" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe>' );
	}

	public function test_rickroll_example_with_insert() {
		register_speed_bump( 'rickroll', array(
			'string_to_inject' => function() { return '<iframe width="420" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe>'; },
			'from_start' => 2,
			'from_end' => false,
			'from_element' => false,
			'minimum_content_length' => array( 'characters' => 1200 ),
		) );
		add_filter( 'the_content', 'insert_speed_bumps' );
		$post_id = $this->factory->post->create( array( 'post_content' => $this->get_dummy_content() ) );
		$post = get_post( $post_id );
		$this->assertSpeedBumpAtParagraph( insert_speed_bumps( $post->post_content ), 4, '<iframe width="420" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe>' );
	}

	private function get_dummy_content() {
		$content = <<<EOT
Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.

Separated they live in Bookmarksgrove right at the coast of the Semantics, a large language ocean.

A small river named Duden flows by their place and supplies it with the necessary regelialia. It is a paradisematic country, in which roasted parts of sentences fly into your mouth.

Even the all-powerful Pointing has no control about the blind texts it is an almost unorthographic life One day however a small line of blind text by the name of Lorem Ipsum decided to leave for the far World of Grammar.

The Big Oxmox advised her not to do so, because there were thousands of bad Commas, wild Question Marks and devious Semikoli, but the Little Blind Text didn’t listen.

She packed her seven versalia, put her initial into the belt and made herself on the way.

When she reached the first hills of the Italic Mountains, she had a last view back on the skyline of her hometown Bookmarksgrove, the headline of Alphabet Village and the subline of her own road, the Line Lane.

Pityful a rethoric question ran over her cheek, then she continued her way. On her way she met a copy. The copy warned the Little Blind Text, that where it came from it would have been rewritten a thousand times and everything that was left from its origin would be the word "and" and the Little Blind Text should
EOT;
		return $content;
	}

	private function assertSpeedBumpAtParagraph( $content_to_test, $speed_bump_paragraph, $injected_string ) {
		$parts = preg_split( '/\n\s*\n/', $content_to_test );
		$actual_speed_bump_paragraph = array_search( $injected_string, $parts );

		if ( false === $actual_speed_bump_paragraph ) {
			$this->fail( 'The speed bump is not in the content' );
		}

		$this->assertEquals( $speed_bump_paragraph, ++$actual_speed_bump_paragraph );

	}
}
