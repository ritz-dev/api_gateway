# Use the official Nginx image as the base image
FROM nginx:stable-alpine

# Set the working directory for Nginx configuration
WORKDIR /etc/nginx/conf.d

# Copy your custom Nginx configuration file to the container
COPY nginx/nginx.conf .

#Rename the copied file to default
RUN mv nginx.conf default.conf

# Expose port 80 to the outside world
EXPOSE 80

# Start Nginx server
CMD ["nginx", "-g", "daemon off;"]