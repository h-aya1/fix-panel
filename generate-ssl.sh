#!/bin/bash

# Generate self-signed SSL certificates for development
# Run this script from the project root directory

SSL_DIR="docker/nginx/ssl"
DOMAIN="localhost"

echo "🔐 Generating SSL certificates for development..."

# Create SSL directory if it doesn't exist
mkdir -p $SSL_DIR

# Generate private key
openssl genrsa -out $SSL_DIR/key.pem 2048

# Generate certificate signing request
openssl req -new -key $SSL_DIR/key.pem -out $SSL_DIR/cert.csr -subj "/C=KR/ST=Seoul/L=Seoul/O=EMS Korea/OU=Development/CN=$DOMAIN"

# Generate self-signed certificate
openssl x509 -req -days 365 -in $SSL_DIR/cert.csr -signkey $SSL_DIR/key.pem -out $SSL_DIR/cert.pem

# Clean up CSR file
rm $SSL_DIR/cert.csr

# Set appropriate permissions
chmod 600 $SSL_DIR/key.pem
chmod 644 $SSL_DIR/cert.pem

echo "✅ SSL certificates generated successfully!"
echo "   Certificate: $SSL_DIR/cert.pem"
echo "   Private Key: $SSL_DIR/key.pem"
echo ""
echo "⚠️  These are self-signed certificates for development only."
echo "   For production, use certificates from a trusted CA."
