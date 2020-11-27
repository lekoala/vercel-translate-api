A vercel powered google translate proxy
===

Requests are made on the public google translate api then cached by the edge network

Simply make request to this endpoint:

    /translate/{to}[/{from}]?text=your_url_encoded_text

Sample response:

    {
        "data": {
            "from": "auto",
            "to": "fr",
            "text": "hello",
            "translation": "bonjour"
        }
    }
