class Hash
{
    static async SHA256(Message)
    {
        const Buffer = new TextEncoder('utf-8').encode(Message);
        const HashBuffer = await crypto.subtle.digest('SHA-256', Buffer);
        const HashArray = Array.from(new Uint8Array(HashBuffer));
        return HashArray.map(b => ('00' + b.toString(16)).slice(-2)).join('');
    }

    static async SHA512(Message)
    {
        const Buffer = new TextEncoder('utf-8').encode(Message);
        const HashBuffer = await crypto.subtle.digest('SHA-512', Buffer);
        const HashArray = Array.from(new Uint8Array(HashBuffer));
        return HashArray.map(b => ('00' + b.toString(16)).slice(-2)).join('');
    }
}