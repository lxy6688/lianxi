def app(environ, start_response):
    data = b"hello ,world!\n"
    start_response("200 ok",[
        ("Content-Type","text/plain"),
        ("Content-Length",str(len(data)))
    ])
    return iter([data])