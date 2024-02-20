# gpt-to-nlp-bridge
Use OpenAI's request format for communicating with NLP Cloud's chatbot API

The program is designed to be integrated into systems where there's a need to interface with NLP Cloud's API while maintaining compatibility with existing OpenAI-based user interfaces.

## Example Usage

```bat
curl http://localhost:8000/v1/chat/completions/ \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer $NLPCLOUD_TOKEN" \
    -d '{
        "model": "chatdolphin",
        "messages": [
            {"role": "system", "content": "You are a helpful assistant."},
            {"role": "user", "content": "Who won the world series in 2020?"},
            {"role": "assistant", "content": "The Los Angeles Dodgers won the World Series in 2020."},
            {"role": "user", "content": "Where was it played?"}
        ]
    }'
```

### Output

```json
{
    "id": "chatcmpl-7QyqpwdfhqwajicIEznoc6Q47XAyW",
    "object": "chat.completion",
    "created": 1677664795,
    "model": "chatdolphin",
    "system_fingerprint": null,
    "choices": [
      {
        "index": 0,
        "message": {
            "role": "assistant",
            "content": "The 2020 World Series was played in Texas at Globe Life Field in Arlington."
        },
        "logprobs": null,
        "finish_reason": "stop",
      }
    ],
    "usage": {
      "completion_tokens": null,
      "prompt_tokens": null,
      "total_tokens": null
    }
}
```

## How It Works

The program expects an OpenAI-based request format such as the following data

```json
{
    "model": "chatdolphin",
    "messages": [
        {"role": "system", "content": "You are a helpful assistant."},
        {"role": "user", "content": "Who won the world series in 2020?"},
        {"role": "assistant", "content": "The Los Angeles Dodgers won the World Series in 2020."},
        {"role": "user", "content": "Where was it played?"}
    ]
}
```

The data is converted to be passed onto NLP Cloud's API that expects the following format

```json
{
    "input": "Where was it played?",
    "context": "You are a helpful assistant.",
    "history": [
        {"input": "Who won the world series in 2020?", "response": "The Los Angeles Dodgers won the World Series in 2020."}
    ]
}
```

The API possibly returns the following data

```json
{
    "response": "The 2020 World Series was played in Texas at Globe Life Field in Arlington.",
    "history": [
        {"input": "Who won the world series in 2020?", "response": "The Los Angeles Dodgers won the World Series in 2020."},
        {"input": "Where was it played?", "response": "The 2020 World Series was played in Texas at Globe Life Field in Arlington."}
    ]
}
```

The data is converted to be returned to the user in OpenAI response format which is the following data

```json
{
    "id": "chatcmpl-7QyqpwdfhqwajicIEznoc6Q47XAyW",
    "object": "chat.completion",
    "created": 1677664795,
    "model": "chatdolphin",
    "system_fingerprint": null,
    "choices": [
      {
        "index": 0,
        "message": {
            "role": "assistant",
            "content": "The 2020 World Series was played in Texas at Globe Life Field in Arlington."
        },
        "logprobs": null,
        "finish_reason": "stop",
      }
    ],
    "usage": {
      "completion_tokens": null,
      "prompt_tokens": null,
      "total_tokens": null
    }
}
```

## Limitations

1. **Null Values in Properties:**
    - The properties `completion_tokens`, `prompt_tokens`, `total_tokens`, `logprobs`, and `system_fingerprint` will always be null. This is because the NLP Cloud API does not return any values for these properties.
    - The absence of these values is due to the fact that the NLP Cloud API does not support features such as displaying log probabilities or providing token counts related to completion or prompt tokens.

2. **Single Element in Choices:**
    - The `choices` property will always contain a single element in the response. This is a result of the NLP Cloud API not having a parameter analogous to OpenAI's `n` parameter.
    - OpenAI's `n` parameter controls the number of chat completion choices to generate for each input message. However, the NLP Cloud API does not offer a similar parameter, thus restricting the number of choices to one per message.
  
3. **Limited Request Data Parsing:**
    - The program will only read the properties `model` and `messages` from the request data. Any additional properties or nested structures in the request data will be ignored.
