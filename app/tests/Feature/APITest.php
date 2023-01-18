<?php

it('starts', function () {
   $this->get('/ping')->assertResponse(200);
});
