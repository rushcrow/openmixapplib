(function() {
    'use strict';

    var default_settings = {
        providers: {
            'foo': {
                'cname': 'www.foo.com'
            },
            'bar': {
                'cname': 'www.bar.com'
            }
        },
        default_ttl: 20
    };

    module('do_init');

    function test_do_init(i) {
        return function() {

            var sut = new OpenmixApplication(i.settings || default_settings),
                config = {
                    requireProvider: this.stub()
                },
                test_stuff = {
                    instance: sut,
                    config: config
                };

            i.setup(test_stuff);

            // Test
            sut.do_init(config);

            // Assert
            i.verify(test_stuff);
        };
    }

    test('change me', test_do_init({
        setup: function(i) {
            // Setup code here
        },
        verify: function(i) {
            // Assertion code here
        }
    }));

    module('handle_request');

    function test_handle_request(i) {
        return function() {
            var sut = new OpenmixApplication(i.settings || default_settings),
                config = {
                    requireProvider: this.stub()
                },
                request = {
                    getData: this.stub(),
                    getProbe: this.stub()
                },
                response = {
                    respond: this.stub(),
                    setTTL: this.stub(),
                    setReasonCode: this.stub()
                },
                test_stuff = {
                    instance: sut,
                    request: request,
                    response: response
                };

            i.setup(test_stuff);

            // Test
            sut.handle_request(request, response);

            // Assert
            i.verify(test_stuff);
        };
    }

    test('change me', test_handle_request({
        setup: function(i) {
            // Setup code here
        },
        verify: function(i) {
            // Assertion code here
        }
    }));

}());
