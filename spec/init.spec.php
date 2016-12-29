<?php

describe('usage', function() {

	it('should do nothing', function() {
		expect(0)->toBe(0);
	});

	it('should bootstrap processwire', function() {
		expect($this->processwire)->toBeAn('object');
	});

	using('migrations', function() {
		it('should migrate for each test', function() {
			expect(0)->toBe(0);
		});
		it('should migrate for each test', function() {
			expect(0)->toBe(0);
		});
		it('should migrate for each test', function() {
			expect($this->processwire)->toBeAn('object');
		});
	});

	using('transaction', function() {
		it('should migrate for each test', function() {
			expect(0)->toBe(0);
		});
		it('should migrate for each test', function() {
			expect(0)->toBe(0);
		});
		it('should migrate for each test', function() {
			expect($this->processwire)->toBeAn('object');
		});
	});

	using(['migration', 'transaction'], function() {
		it('should migrate for each test', function() {
			expect(0)->toBe(0);
		});
		it('should migrate for each test', function() {
			expect(0)->toBe(0);
		});
		xit('should migrate for each test', function() {
			expect($this->processwire)->toBeAn('object');
		});
	});
});