import cStringIO as StringIO
from random import randint
import sys
from PIL import Image
import wand.image


input_filepath = sys.argv[1]
img = Image.open(input_filepath)
star = Image.open(sys.argv[2])

frames = []
open_buffs = []
theta = 10
inc = 2

transform = (range(0, theta, inc) + range(-theta, theta, inc)[::-1] + range(-theta, 0, theta))
frame_number = 0
star_positions = None
star_rotations = None

num_stars = 10

for d in transform:
    t_img = img.rotate(d)

    if frame_number % 2 == 0:
        star_positions = [tuple((randint(0, t_img.size[i] - star.size[i]) for i in range(2))) for i in range(num_stars)]
        star_rotations = [randint(0, 360) for i in range(num_stars)]

    for s in range(num_stars):
        t_star = star.rotate(star_rotations[s])
        t_img.paste(t_star, star_positions[s], mask=t_star)

    frame_number += 1

    buff = StringIO.StringIO()
    t_img.save(buff, "JPEG", quality=10)
    buff.seek(0)
    frames.append(Image.open(buff))
    open_buffs.append(buff)

gif = wand.image.Image(width=img.size[0], height=img.size[1])

for frame in frames[::-1]:
    f_buff = StringIO.StringIO()
    frame.save(f_buff, "JPEG")
    f_buff.seek(0)
    gif.sequence.insert(0, wand.image.Image(file=f_buff))
    f_buff.close()

gif.save(filename=input_filepath + ".gif")

for buff in open_buffs:
    buff.close()

