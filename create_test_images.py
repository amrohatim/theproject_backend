#!/usr/bin/env python3
"""
Create test images for product color variants
"""

from PIL import Image, ImageDraw, ImageFont
import os

def create_test_image(color_name, color_rgb, filename):
    """Create a simple test image with the specified color"""
    # Create a 300x400 image (as specified in the form)
    width, height = 300, 400
    image = Image.new('RGB', (width, height), color_rgb)
    
    # Add text overlay
    draw = ImageDraw.Draw(image)
    
    try:
        # Try to use a default font
        font = ImageFont.truetype("arial.ttf", 40)
    except:
        # Fallback to default font
        font = ImageFont.load_default()
    
    # Add color name text
    text = f"{color_name}\nTest Image"
    
    # Calculate text position (center)
    bbox = draw.textbbox((0, 0), text, font=font)
    text_width = bbox[2] - bbox[0]
    text_height = bbox[3] - bbox[1]
    
    x = (width - text_width) // 2
    y = (height - text_height) // 2
    
    # Add white text with black outline for visibility
    outline_color = (0, 0, 0) if sum(color_rgb) > 384 else (255, 255, 255)
    text_color = (255, 255, 255) if sum(color_rgb) < 384 else (0, 0, 0)
    
    # Draw text outline
    for dx in [-1, 0, 1]:
        for dy in [-1, 0, 1]:
            if dx != 0 or dy != 0:
                draw.text((x + dx, y + dy), text, font=font, fill=outline_color)
    
    # Draw main text
    draw.text((x, y), text, font=font, fill=text_color)
    
    # Save the image
    image.save(filename, 'JPEG', quality=95)
    print(f"Created test image: {filename}")

def main():
    """Create test images for red and blue colors"""
    
    # Create red test image
    create_test_image("Red", (255, 0, 0), "red_test_image.jpg")
    
    # Create blue test image  
    create_test_image("Blue", (0, 0, 255), "blue_test_image.jpg")
    
    print("Test images created successfully!")
    print("Files created:")
    print("- red_test_image.jpg")
    print("- blue_test_image.jpg")

if __name__ == "__main__":
    main()
